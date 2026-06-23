<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',

                    // Kalau sistem awak ada role, guna ini:
                    'role' => 'user',
                ]);
            }

            Auth::login($user);

            /*
            |--------------------------------------------------------------------------
            | Redirect selepas login
            |--------------------------------------------------------------------------
            | Kalau profile belum lengkap, hantar ke page profile.
            | Kalau sudah lengkap, hantar ke dashboard.
            |--------------------------------------------------------------------------
            */

            if (method_exists($user, 'profile') && !$user->profile) {
                return redirect()->route('profile.create')
                    ->with('info', 'Sila lengkapkan profil anda dahulu.');
            }

            return redirect()->route('user.dashboard')
                ->with('success', 'Berjaya log masuk menggunakan akaun Google.');

        } catch (Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Log masuk Google tidak berjaya. Sila cuba semula.');
        }
    }
}