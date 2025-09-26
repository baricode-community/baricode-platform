<?php

namespace App\Http\Controllers;

use App\Models\User\User;
use App\Services\WhatsAppService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                        $fail('Nomor WhatsApp tidak terdaftar di WhatsApp (contoh: 08123456789).');
                    }
                },
            ],
            'about' => ['nullable', 'string', 'max:50000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted', 'required'],
        ]);

        $user = User::create([
            'name' => $request->name,
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

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
