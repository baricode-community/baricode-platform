<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
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
            
            // Generate unique username
            $username = $this->generateUniqueUsername($googleUser->getName());
            
            $user = User::create([
                'name' => $googleUser->getName(),
                'username' => $username,
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

    /**
     * Generate unique username from name
     */
    private function generateUniqueUsername(string $name): string
    {
        // Remove special characters and convert to lowercase
        $baseUsername = Str::slug($name, '');
        
        // Ensure minimum length of 3 characters
        if (strlen($baseUsername) < 3) {
            $baseUsername = $baseUsername . 'usr';
        }
        
        // Ensure maximum length of 30 characters
        if (strlen($baseUsername) > 30) {
            $baseUsername = substr($baseUsername, 0, 30);
        }
        
        return $this->ensureUniqueUsername($baseUsername);
    }

    /**
     * Ensure username is unique by adding numbers if necessary
     */
    private function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $suffix = (string) $counter;
            $maxBaseLength = 30 - strlen($suffix);
            
            if (strlen($baseUsername) > $maxBaseLength) {
                $username = substr($baseUsername, 0, $maxBaseLength) . $suffix;
            } else {
                $username = $baseUsername . $suffix;
            }
            
            $counter++;
        }
        
        return $username;
    }
}
