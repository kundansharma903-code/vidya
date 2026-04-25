<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->dashboardForRole(Auth::user()->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Invalid credentials. Please try again.'])->onlyInput('login');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors(['login' => 'Your account has been deactivated. Contact your administrator.']);
        }

        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect($this->dashboardForRole($user->role));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardForRole(string $role): string
    {
        return match ($role) {
            'owner'         => '/owner/dashboard',
            'academic_head' => '/academic-head/dashboard',
            'admin'         => '/admin/dashboard',
            'sub_admin'     => '/sub-admin/dashboard',
            'teacher'       => '/teacher/dashboard',
            'typist'        => '/typist/dashboard',
            default         => '/login',
        };
    }
}
