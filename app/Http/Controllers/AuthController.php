<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginForm() { return view('auth.login'); }
    public function login(Request $request) {
        session(['user_name' => 'Demo User', 'role' => 'Landlord']);
        return redirect('/dashboard/landlord');
    }
    public function registerForm(Request $request) {
        return view('auth.register', ['role' => $request->role]);
    }
    public function register(Request $request) {
        session(['user_name' => $request->name ?? 'New User', 'role' => ucfirst($request->role ?? 'Landlord')]);
        return redirect('/dashboard/' . ($request->role ?? 'landlord'));
    }
    public function logout() {
        session()->flush();
        return redirect('/');
    }
}
