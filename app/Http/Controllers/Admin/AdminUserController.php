<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"));
        }
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('status', $request->status);

        $users = $query->withCount(['listings', 'clientBookings'])->latest()->paginate(25)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['wallet', 'kycDocuments', 'listings', 'clientBookings' => fn($q) => $q->latest()->limit(5)]);
        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        abort_if($user->role === 'super_admin', 403);
        $user->update(['status' => 'suspended']);
        return back()->with('success', "User {$user->name} suspended.");
    }

    public function verify(User $user)
    {
        $user->update(['status' => 'verified']);
        return back()->with('success', "User {$user->name} verified.");
    }
}
