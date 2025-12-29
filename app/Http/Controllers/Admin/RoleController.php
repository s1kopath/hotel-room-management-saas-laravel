<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\DataTables\RolesDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Get all permissions
        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');

        // Hotel owners can only create custom roles
        // Super admin can create system roles
        $canCreateSystemRole = $user->isSuperAdmin();

        return view('roles.components.create', compact('permissions', 'canCreateSystemRole'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            // Determine scope based on user type
            $scope = 'custom'; // Default for hotel owners

            if ($user->isSuperAdmin() && $request->has('scope')) {
                $scope = $request->scope; // Super admin can set scope
            } elseif ($user->isHotelOwner()) {
                $scope = 'hotel_owner'; // Force hotel_owner scope for owners
            }

            // Generate unique slug
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug is unique
            while (Role::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $role = Role::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'scope' => $scope,
                'created_by' => Auth::id(),
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            DB::commit();

            return to_route('roles.index')
                ->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with(['permissions', 'createdBy'])->findOrFail($id);

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            // Hotel owners can only view their own roles
            if ($role->scope === 'system' || ($role->scope === 'hotel_owner' && $role->created_by !== $user->id)) {
                abort(403, 'You do not have access to this role.');
            }
        }

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            // Hotel owners cannot edit system roles
            if ($role->scope === 'system') {
                abort(403, 'Cannot edit system roles.');
            }
            // Hotel owners can only edit their own roles
            if ($role->scope === 'hotel_owner' && $role->created_by !== $user->id) {
                abort(403, 'You do not have access to this role.');
            }
        }

        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.components.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            // Hotel owners cannot edit system roles
            if ($role->scope === 'system') {
                abort(403, 'Cannot edit system roles.');
            }
            // Hotel owners can only edit their own roles
            if ($role->scope === 'hotel_owner' && $role->created_by !== $user->id) {
                abort(403, 'You do not have access to this role.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            // Generate slug if name changed
            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
            ];

            if ($role->name !== $request->name) {
                $slug = Str::slug($request->name);
                $originalSlug = $slug;
                $counter = 1;
                
                // Ensure slug is unique (excluding current role)
                while (Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $updateData['slug'] = $slug;
            }

            $role->update($updateData);

            // Sync permissions
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();

            return to_route('roles.index')
                ->with('success', 'Role updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            // Hotel owners cannot delete system roles
            if ($role->scope === 'system') {
                abort(403, 'Cannot delete system roles.');
            }
            // Hotel owners can only delete their own roles
            if ($role->scope === 'hotel_owner' && $role->created_by !== $user->id) {
                abort(403, 'You do not have access to this role.');
            }
        }

        // Prevent deleting system roles
        if ($role->scope === 'system' && !$user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete system roles.');
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        DB::beginTransaction();
        try {
            // Detach all permissions
            $role->permissions()->detach();

            // Delete role
            $role->delete();

            DB::commit();

            return to_route('roles.index')
                ->with('success', 'Role deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
}
