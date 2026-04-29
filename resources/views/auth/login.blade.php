<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-xl font-semibold text-slate-900">{{ __('Welcome Back') }}</h2>
        <p class="mt-1 text-sm text-slate-600">{{ __('Sign in to your Digital Life Manager') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-password-input id="password" class="block mt-1 w-full" name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-5 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                    <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="link text-sm" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>

            @if (Route::has('register'))
                <p class="text-center text-sm text-slate-600">
                    <span>{{ __('New here?') }}</span>
                    <a class="link ms-1 font-medium" href="{{ route('register') }}">{{ __('Create an account') }}</a>
                </p>
            @endif
        </div>
    </form>
</x-guest-layout>
