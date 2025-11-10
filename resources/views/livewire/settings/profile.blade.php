<?php

use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Services\WhatsAppService;

new class extends Component {
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $whatsapp = '';
    public string $about = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->username = Auth::user()->username ?? '';
        $this->email = Auth::user()->email;
        $this->whatsapp = Auth::user()->whatsapp ?? '';
        $this->about = Auth::user()->about ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9]+$/',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'whatsapp' => [
                'nullable',
                'string',
                Rule::unique('users', 'whatsapp')->ignore($user->id),
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $value !== $user->whatsapp && !WhatsAppService::isValidNumber($value)) {
                        $fail('Nomor WhatsApp tidak terdaftar di WhatsApp.');
                    }
                },
            ],
            'about' => ['nullable', 'string', 'max:50000'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Generate a suggested username based on the current name
     */
    public function generateSuggestedUsername(): void
    {
        $baseName = $this->name ?: Auth::user()->name;
        $baseUsername = \Illuminate\Support\Str::slug($baseName, '');
        
        // Ensure minimum length
        if (strlen($baseUsername) < 3) {
            $baseUsername = $baseUsername . 'user';
        }
        
        // Ensure maximum length
        if (strlen($baseUsername) > 30) {
            $baseUsername = substr($baseUsername, 0, 30);
        }
        
        // Make it unique
        $username = $baseUsername;
        $counter = 1;
        $currentUserId = Auth::user()->id;
        
        while (User::where('username', $username)->where('id', '!=', $currentUserId)->exists()) {
            $suffix = (string) $counter;
            $maxBaseLength = 30 - strlen($suffix);
            
            if (strlen($baseUsername) > $maxBaseLength) {
                $username = substr($baseUsername, 0, $maxBaseLength) . $suffix;
            } else {
                $username = $baseUsername . $suffix;
            }
            
            $counter++;
        }
        
        $this->username = $username;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                <div class="flex gap-2">
                    <flux:input 
                        wire:model="username" 
                        type="text" 
                        required 
                        autocomplete="username"
                        placeholder="contoh: johnsmith123"
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/[^a-z0-9]/g, '')"
                        class="flex-1"
                    />
                    <flux:button 
                        wire:click="generateSuggestedUsername" 
                        type="button" 
                        variant="outline" 
                        size="sm"
                        class="whitespace-nowrap"
                    >
                        Generate
                    </flux:button>
                </div>
                <flux:text class="mt-1 text-sm text-gray-500">
                    Username harus 3-30 karakter, hanya huruf kecil dan angka (tanpa spasi atau karakter khusus).
                </flux:text>
                @if(Auth::user()->username)
                    <flux:text class="mt-1 text-xs text-blue-600">
                        Username saat ini: <strong>{{ Auth::user()->username }}</strong>
                    </flux:text>
                @endif
            </div>

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:input wire:model="whatsapp" :label="__('WhatsApp Number')" type="text" autocomplete="whatsapp" />

            <flux:textarea wire:model="about" :label="__('About Me')" rows="4" autocomplete="about" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
