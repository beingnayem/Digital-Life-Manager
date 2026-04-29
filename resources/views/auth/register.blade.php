<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-xl font-semibold text-slate-900">{{ __('Create Your Account') }}</h2>
        <p class="mt-1 text-sm text-slate-600">{{ __('Start organizing your life with Digital Life Manager') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-password-input id="password" class="block mt-1 w-full" name="password" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-password-input id="password_confirmation" class="block mt-1 w-full" name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-5 space-y-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Create account') }}
            </x-primary-button>

            <p class="text-center text-sm text-slate-600">
                <span>{{ __('Already have an account?') }}</span>
                <a class="link ms-1 font-medium" href="{{ route('login') }}">{{ __('Log in') }}</a>
            </p>
        </div>
    </form>
</x-guest-layout>
