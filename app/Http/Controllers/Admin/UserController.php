<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;

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
        $user = User::with(['parentUser', 'createdBy', 'roles'])->findOrFail($id);
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
}
