<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Menampilkan halaman registrasi
    public function showRegister(): View
    {
        return view('auth.register');
    }

    // Memproses registrasi pengguna
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => ['nullable', 'string', 'max:20'],

            'role' => [
                'required',
                'in:customer,provider',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        // Membuat akun pengguna baru
        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->password = $validated['password'];

        $user->save();

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Memperbarui session untuk keamanan
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Bantuin.');
    }

    // Menampilkan halaman login
    public function showLogin(): View
    {
        return view('auth.login');
    }

    // Memproses login pengguna
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password yang dimasukkan salah.',
            ])
            ->onlyInput('email');
    }

    // Mengarahkan pengguna berdasarkan role
    public function dashboard(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        if ($user->role === 'provider') {
            return redirect()->route('provider.dashboard');
        }

        abort(403, 'Role pengguna tidak valid.');
    }

    // Memproses logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Kamu berhasil logout.');
    }
}