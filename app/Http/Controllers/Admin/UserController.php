<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user       = User::findOrFail($id);
        $properties = Property::where('user_id', $id)->latest()->take(10)->get();
        $leases     = Lease::with('property:id,title')
            ->where(function ($q) use ($id) {
                $q->where('tenant_id', $id)->orWhere('landlord_id', $id);
            })
            ->latest()->take(10)->get();
        $payments   = RentPayment::where('tenant_id', $id)->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'properties', 'leases', 'payments'));
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        $msg = $user->is_active ? "User \"{$user->name}\" activated." : "User \"{$user->name}\" deactivated.";
        return redirect()->route('admin.users.index')->with('success', $msg);
    }
}
