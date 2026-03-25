<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
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
        $allowedRoles = Auth::user()->role === 'superadmin'
            ? 'admin,superadmin,supplier,dapur'
            : 'admin,supplier,dapur';

        $request->validate([
            'role' => 'required|in:' . $allowedRoles,
        ]);

        if (Auth::id() === $user->id) {
            return back()->with('error', 'Role akun sendiri tidak dapat diubah dari halaman ini.');
        }

        $oldRole = $user->role;

        $user->update([
            'role' => $request->role
        ]);

        ActivityLogger::log(
            'user.role_updated',
            'Role user ' . $user->name . ' diubah dari ' . $oldRole . ' ke ' . $request->role,
            $user,
            ['from' => $oldRole, 'to' => $request->role]
        );

        return back()->with('success', 'Role ' . $user->name . ' berhasil diubah!');
    }
}
