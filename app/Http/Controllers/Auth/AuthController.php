<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginValue = trim($data['login']);
        $password = (string) $data['password'];
        $remember = $request->boolean('remember');
        $user = null;

        if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
            $normalizedEmail = Str::lower($loginValue);
            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
                ->first();
        } else {
            $normalizedUsername = Str::lower($loginValue);
            $user = User::query()
                ->whereNotNull('username')
                ->whereRaw('LOWER(username) = ?', [$normalizedUsername])
                ->first();
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['login' => 'Kredentzialak ez dira baliozkoak.'])
                ->onlyInput('login');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        $user = User::create([
            'name' => trim((string) $data['name']),
            'username' => Str::lower(trim((string) $data['username'])),
            'email' => Str::lower(trim((string) $data['email'])),
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
