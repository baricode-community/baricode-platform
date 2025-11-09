<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use App\Services\WhatsAppService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'whatsapp' => [
                'nullable',
                'string',
                'unique:users,whatsapp',
                function ($attribute, $value, $fail) {
                    if ($value && !WhatsAppService::isValidNumber($value)) {
                        $fail('Nomor WhatsApp tidak terdaftar di WhatsApp.');
                    }
                },
            ],
            'about' => ['nullable', 'string', 'max:50000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted', 'required'],
        ]);

        // Generate unique username based on name
        $username = $this->generateUniqueUsername($request->name);

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp' => $request->whatsapp,
            'about' => $request->about,
            'tos_accepted_at' => now(),
        ]);
        $user->assignRole('student');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
