<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-semibold text-slate-900">
                {{ config('app.name', 'Digital Life Manager') }}
            </h1>

            <p class="mt-2 text-sm text-slate-600">
                Manage tasks, expenses, notes, and mood tracking in one place.
            </p>
        </div>

        @if (Route::has('login'))
            <div class="flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary w-full">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary w-full">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary w-full">
                            Create account
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        <p class="text-center text-xs text-slate-500">
            Secure, fast, and built with Laravel.
        </p>
    </div>
</x-guest-layout>
