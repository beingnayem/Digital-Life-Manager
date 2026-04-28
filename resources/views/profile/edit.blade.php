<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="page">
        <div class="page-container space-y-6">
            <div class="card">
                <div class="card-body-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body-sm">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body-sm">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
