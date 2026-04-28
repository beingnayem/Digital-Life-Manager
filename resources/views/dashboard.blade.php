<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="card">
                <div class="card-body">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
