<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $whatsapp = null;
    public string $level = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]{7,20}$/', 'unique:' . User::class],
            'level' => ['required', 'string', 'in:pemula,menengah,mahir'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-5">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Full name')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email"
            :placeholder="__('email@example.com')" />

        <!-- WhatsApp Number -->
        <flux:input wire:model="whatsapp" :label="__('WhatsApp Number')" type="tel" autocomplete="tel"
            :placeholder="__('e.g. +628123456789')" />

        <!-- Level -->
        <div class="flex flex-col gap-2">
            <label for="level" class="font-medium text-sm text-zinc-700 dark:text-zinc-200">
            {{ __('Level') }}
            </label>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">
            {{ __('Pilih tingkat kemampuan Anda untuk pengalaman belajar yang sesuai.') }}
            </span>
            <span class="text-xs text-yellow-600 dark:text-yellow-400 mb-1">
            {{ __('Level tidak dapat diubah setelah pendaftaran. Pastikan pilihan Anda sudah benar.') }}
            </span>
            <select id="level" name="level" wire:model="level" required
            class="rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 focus:border-primary-500 focus:ring-primary-500">
            <option value="" disabled selected>{{ __('Select level') }}</option>
            <option value="pemula">Pemula</option>
            <option value="menengah">Menengah</option>
            <option value="mahir">Mahir</option>
            </select>
            @error('level')
            <span class="text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password"
            :placeholder="__('Password')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

        <div class="flex items-center justify-end mt-2">
            <flux:button type="submit" variant="primary"
                class="w-full transition-all duration-150 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 rounded-lg shadow"
                data-test="register-user-button">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="flex items-center my-2">
        <div class="flex-grow border-t border-zinc-200 dark:border-zinc-700"></div>
        <span class="mx-3 text-xs text-zinc-400">{{ __('or') }}</span>
        <div class="flex-grow border-t border-zinc-200 dark:border-zinc-700"></div>
    </div>

    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate class="text-primary-600 hover:underline font-medium">
            {{ __('Log in') }}
        </flux:link>
    </div>
</div>
