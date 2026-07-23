<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
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
                    'name' => $user->name ?: ($googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google'),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',
                    'role' => $user->role ?: 'user',
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'provider' => 'google',
                    'role' => 'user',

                    // Biar null dulu supaya user wajib lengkapkan profil
                    'account_type' => null,
                ]);
            }

            Auth::login($user);

            /*
            |--------------------------------------------------------------------------
            | Check Profile
            |--------------------------------------------------------------------------
            | Kalau user Google belum ada profile, hantar ke step 1.
            */
            $hasProfile = UserProfile::where('user_id', $user->id)->exists();

            if (!$hasProfile) {
                return redirect()
                    ->route('user.profile.create.step1')
                    ->with('info', 'Sila lengkapkan profil anda dahulu.');
            }

            return redirect()
                ->route('user.dashboard')
                ->with('success', 'Berjaya log masuk menggunakan akaun Google.');

        } catch (Throwable $e) {
            return redirect()
                ->route('login')
                ->with('error', 'Log masuk Google tidak berjaya. Sila cuba semula.');
        }
    }
}