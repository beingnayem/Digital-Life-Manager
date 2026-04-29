<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-xl font-semibold text-slate-900">{{ __('Confirm Your Password') }}</h2>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-password-input id="password" class="block mt-1 w-full" name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
