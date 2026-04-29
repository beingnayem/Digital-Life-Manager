<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    @if (session('status') === 'account-deletion-link-sent')
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ __('We sent a confirmation email. Please check your inbox to finish deleting your account.') }}
        </div>
    @endif

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.account-deletion.request') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-slate-900">
                {{ __('Confirm account deletion request') }}
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                {{ __('For security, we will send a confirmation email with an expiring link. Enter your password to request the deletion email.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-password-input
                    id="password"
                    name="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Send Confirmation Email') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
