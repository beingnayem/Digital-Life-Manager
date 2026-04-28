@props([
    'title' => 'No items found',
    'description' => 'There is nothing here yet.',
])

<div class="empty-state">
    <div class="empty-state-icon">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M4 7.5A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5v9A2.5 2.5 0 0117.5 19h-11A2.5 2.5 0 014 16.5v-9z" />
        </svg>
    </div>

    <h3 class="empty-state-title">{{ $title }}</h3>
    <p class="empty-state-description">{{ $description }}</p>

    @if ($slot->isNotEmpty())
        <div class="mt-5 flex items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
