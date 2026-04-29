@props(['disabled' => false])

<div x-data="{ visible: false }" class="relative">
    <input
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'form-input pr-12']) }}
        type="password"
        x-bind:type="visible ? 'text' : 'password'"
    >

    <button
        type="button"
        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 transition hover:text-slate-700"
        @click="visible = !visible"
        :aria-label="visible ? 'Hide password' : 'Show password'"
    >
        <svg x-show="!visible" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>

        <svg x-show="visible" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.446-4.11M6.423 6.423A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.132 5.411" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 9.879a3 3 0 104.242 4.242" />
        </svg>
    </button>
</div>