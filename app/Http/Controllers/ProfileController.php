<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil pengguna yang sedang login.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Simpan perubahan biodata, kontak, dan avatar pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada di storage public
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Tampilkan halaman profil publik pengguna.
     */
    public function show(User $user): View
    {
        // Muat relasi listing dan review sesuai role pengguna
        $offers = $user->role === 'provider'
            ? $user->serviceOffers()->where('status', 'active')->with('category')->latest()->get()
            : collect();

        $requests = $user->role === 'customer'
            ? $user->serviceRequests()->where('status', 'open')->with('category')->latest()->get()
            : collect();

        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->get();

        return view('profile.show', compact('user', 'offers', 'requests', 'reviews'));
    }
}

