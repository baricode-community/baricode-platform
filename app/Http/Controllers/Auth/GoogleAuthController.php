<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Notifications\WelcomeEmailWithPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle(): RedirectResponse
    {
        logger()->info('Redirecting to Google OAuth');
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        logger()->info('Handling Google OAuth callback');
        try {
            $googleUser = Socialite::driver('google')->user();
            logger()->info('Google User Retrieved: ' . $googleUser->getEmail());

            // Check if user already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // User exists, just login
                logger()->info('Existing user found: ' . $existingUser->email);
                Auth::login($existingUser);
                return redirect()->intended(route('dashboard'));
            }

            // Create new user
            $generatedPassword = Str::random(12);
            logger()->info('Creating new user: ' . $googleUser->getEmail());
            
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make($generatedPassword),
                'email_verified_at' => now(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            logger()->info('New user created: ' . $user->email);
            
            // Send welcome email with generated password
            $user->notify(new WelcomeEmailWithPassword($generatedPassword));
            logger()->info('Welcome email sent to: ' . $user->email);
            
            // Login the user
            Auth::login($user);
            logger()->info('User logged in: ' . $user->email);
            
            return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Password telah dikirim ke email Anda.');
            
        } catch (\Exception $e) {
            logger()->error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }
}
