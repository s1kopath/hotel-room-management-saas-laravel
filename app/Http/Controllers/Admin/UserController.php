<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UsersDataTable $dataTable)
    {
        return  $dataTable->render('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.components.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|string|email|max:100|unique:users,email',
            'full_name' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:super_admin,hotel_owner,staff',
            'status' => 'required|in:active,suspended,deleted',
            'parent_user_id' => 'nullable|exists:users,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'user_type' => $request->user_type,
            'status' => $request->status,
            'parent_user_id' => $request->parent_user_id,
            'created_by' => Auth::id(),
        ]);

        // Log activity
        app(ActivityLogService::class)->logUserCreated($user->id, $user->username);

        return to_route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['parentUser', 'createdBy', 'roles', 'hotels', 'accessibleHotels'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        // Get hotel owners for parent_user_id dropdown (if staff)
        $hotelOwners = User::where('user_type', 'hotel_owner')
            ->where('status', 'active')
            ->get();

        return view('users.components.edit', compact('user', 'hotelOwners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|string|email|max:100|unique:users,email,' . $id,
            'full_name' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:super_admin,hotel_owner,staff',
            'status' => 'required|in:active,suspended,deleted',
            'parent_user_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'user_type' => $request->user_type,
            'status' => $request->status,
            'parent_user_id' => $request->parent_user_id,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        // Log activity
        app(ActivityLogService::class)->logUserUpdated($user->id, $user->username);

        return to_route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting super admin
        if ($user->isSuperAdmin()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete super admin user.'], 403);
            }
            return back()->with('error', 'Cannot delete super admin user.');
        }

        // Soft delete by setting status to 'deleted' instead of actually deleting
        $username = $user->username;
        $userId = $user->id;
        $user->update(['status' => 'deleted']);

        // Log activity
        app(ActivityLogService::class)->logUserDeleted($userId, $username);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }

        return to_route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Show the form for editing user roles
     */
    public function editRoles(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $currentUser = Auth::user();

        // Check permissions
        // Super admin can assign roles to anyone
        // Hotel owners can only assign roles to their staff
        if (!$currentUser->isSuperAdmin()) {
            if ($user->isSuperAdmin()) {
                abort(403, 'You cannot assign roles to super admin users.');
            }

            if ($currentUser->isHotelOwner()) {
                // Hotel owners can only manage their own staff
                if ($user->parent_user_id !== $currentUser->id) {
                    abort(403, 'You can only assign roles to your own staff members.');
                }
            } else {
                // Staff cannot assign roles
                abort(403, 'You do not have permission to assign roles.');
            }
        }

        // Get available roles based on user type
        if ($currentUser->isSuperAdmin()) {
            // Super admin can assign any role
            $availableRoles = Role::with('permissions')->orderBy('scope')->orderBy('name')->get();
        } else {
            // Hotel owners can assign:
            // 1. System roles (scope = 'system')
            // 2. Their own custom roles (scope = 'hotel_owner', hotel_owner_id = current user id)
            $availableRoles = Role::with('permissions')->where(function ($query) use ($currentUser) {
                $query->where('scope', 'system')
                    ->orWhere(function ($q) use ($currentUser) {
                        $q->where('scope', 'hotel_owner')
                            ->where('hotel_owner_id', $currentUser->id);
                    });
            })->orderBy('scope')->orderBy('name')->get();
        }

        return view('users.components.edit-roles', compact('user', 'availableRoles'));
    }

    /**
     * Update user roles
     */
    public function updateRoles(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Check permissions (same as editRoles)
        if (!$currentUser->isSuperAdmin()) {
            if ($user->isSuperAdmin()) {
                abort(403, 'You cannot assign roles to super admin users.');
            }

            if ($currentUser->isHotelOwner()) {
                if ($user->parent_user_id !== $currentUser->id) {
                    abort(403, 'You can only assign roles to your own staff members.');
                }
            } else {
                abort(403, 'You do not have permission to assign roles.');
            }
        }

        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            // Get current roles
            $currentRoleIds = $user->roles()->pluck('roles.id')->toArray();
            $newRoleIds = $request->roles ?? [];

            // Validate that hotel owners can only assign roles they're allowed to
            if (!$currentUser->isSuperAdmin()) {
                $allowedRoleIds = Role::where(function ($query) use ($currentUser) {
                    $query->where('scope', 'system')
                        ->orWhere(function ($q) use ($currentUser) {
                            $q->where('scope', 'hotel_owner')
                                ->where('hotel_owner_id', $currentUser->id);
                        });
                })->pluck('id')->toArray();

                // Check if all new roles are allowed
                $invalidRoles = array_diff($newRoleIds, $allowedRoleIds);
                if (!empty($invalidRoles)) {
                    return back()->withInput()->withErrors(['roles' => 'You do not have permission to assign some of the selected roles.']);
                }
            }

            // Sync roles with pivot data
            $syncData = [];
            foreach ($newRoleIds as $roleId) {
                $syncData[$roleId] = [
                    'assigned_by' => $currentUser->id,
                    'assigned_at' => now(),
                ];
            }

            $user->roles()->sync($syncData);

            // Clear permission cache for this user
            $user->clearPermissionCache();

            // Log activity
            $addedRoles = array_diff($newRoleIds, $currentRoleIds);
            $removedRoles = array_diff($currentRoleIds, $newRoleIds);

            if (!empty($addedRoles) || !empty($removedRoles)) {
                $roleNames = empty($newRoleIds)
                    ? ['None']
                    : Role::whereIn('id', $newRoleIds)->pluck('name')->toArray();

                $description = empty($roleNames) || (count($roleNames) === 1 && $roleNames[0] === 'None')
                    ? "Removed all roles from {$user->username}"
                    : "Assigned roles to {$user->username}: " . implode(', ', $roleNames);

                app(ActivityLogService::class)->log(
                    'assign_roles',
                    'user',
                    $user->id,
                    $description
                );
            }

            DB::commit();

            return to_route('users.show', $user->id)
                ->with('success', 'User roles updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating roles: ' . $e->getMessage());
        }
    }
}
