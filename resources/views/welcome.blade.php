<x-guest-layout>
    <div class="space-y-3">
        @if (Route::has('login'))
            <div class="flex flex-col gap-2 pt-2">
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
    </div>
</x-guest-layout>
