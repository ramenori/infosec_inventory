<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountsController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.accounts', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.accounts')->with('success', 'User account created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.accounts_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $data = $request->all();
        
        // Only hash password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.accounts')->with('success', 'User account updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting the last admin
        if (User::count() == 1) {
            return redirect()->route('admin.accounts')->with('error', 'Cannot delete the only user account!');
        }

        $user->delete();

        return redirect()->route('admin.accounts')->with('success', 'User account deleted successfully!');
    }
}
