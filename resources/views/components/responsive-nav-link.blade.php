@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex w-full items-center rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-start text-sm font-semibold text-primary-700 shadow-sm transition duration-150 ease-in-out hover:bg-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2'
            : 'flex w-full items-center rounded-xl border border-transparent px-4 py-3 text-start text-sm font-medium text-slate-600 transition duration-150 ease-in-out hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
