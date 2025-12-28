<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
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
            'created_by' => auth()->id(),
        ]);

        return to_route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
