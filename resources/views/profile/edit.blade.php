<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Profile Management</p>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container space-y-6">
            <div class="card">
                <div class="card-body-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-lg font-semibold text-primary-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">{{ $user->name }}</h3>
                                <p class="text-sm text-slate-600">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-slate-500">
                            Joined {{ optional($user->created_at)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>

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
