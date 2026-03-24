<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        
       $users = User::where('id', '!=', Auth::id())->get();
        return view('admin.users.index', compact('users'));
    }

    
    public function updateRole(Request $request, User $user) 
    {
        $request->validate([
            'role' => 'required|in:admin,superadmin,supplier,dapur',
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'Role ' . $user->name . ' berhasil diubah!');
    }
}
