<x-app-layout>
    @php
        $emojiMap = [
            'happy' => '😄',
            'sad' => '😢',
            'anxious' => '😰',
            'angry' => '😠',
            'calm' => '😌',
            'excited' => '🤩',
            'tired' => '😴',
            'neutral' => '🙂',
            'stressed' => '😵',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Mood Tracker</p>
            </div>
            <button x-data @click="$dispatch('open-mood-modal', { mode: 'create' })" class="btn-primary">+ Add Mood</button>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container space-y-6">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('moods.index') }}" class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="form-label">Mood Type</label>
                            <select name="mood_type" class="form-input w-full text-sm">
                                <option value="">All moods</option>
                                @foreach ($moodTypes as $type)
                                    <option value="{{ $type }}" {{ request('mood_type') === $type ? 'selected' : '' }}>
                                        {{ $emojiMap[$type] ?? '🙂' }} {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input w-full text-sm">
                        </div>
                        <div>
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input w-full text-sm">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-secondary">Filter</button>
                            <a href="{{ route('moods.index') }}" class="btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <div class="card-body">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-slate-900">Weekly Mood Trend</h2>
                            <p class="text-xs text-slate-500">Last 7 days</p>
                        </div>
                        <div class="h-72">
                            <canvas id="moodTrackerWeeklyChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="text-sm font-semibold text-slate-900">Today Check-in</h2>
                        <p class="mt-1 text-sm text-slate-500">One mood entry per day keeps your trend meaningful.</p>
                        <button x-data @click="$dispatch('open-mood-modal', { mode: 'create' })" class="btn-primary mt-4 w-full">Record Mood</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-slate-900">Mood History Timeline</h2>
                        <span class="text-xs text-slate-500">Most recent first</span>
                    </div>

                    @if ($moods->count())
                        <ol class="relative ml-2 border-l border-slate-200">
                            @foreach ($moods as $mood)
                                <li class="mb-6 ml-6">
                                    <span class="absolute -left-[11px] flex h-5 w-5 items-center justify-center rounded-full border border-slate-200 bg-white text-xs">
                                        {{ $emojiMap[$mood->mood_label] ?? '🙂' }}
                                    </span>

                                    <div class="rounded-xl border border-slate-200/70 bg-slate-50/60 p-4">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900">{{ ucfirst($mood->mood_label ?? 'neutral') }}</p>
                                                <span class="rounded-lg bg-white px-2 py-0.5 text-xs text-slate-500">Level {{ $mood->mood_level }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500">{{ optional($mood->recorded_date)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-4">{{ $moods->links() }}</div>
                    @else
                        <x-empty-state
                            title="No mood entries yet"
                            description="Track your daily mood to build a meaningful weekly trend and spot patterns over time."
                        >
                            <button x-data @click="$dispatch('open-mood-modal', { mode: 'create' })" class="btn-primary">Record Mood</button>
                        </x-empty-state>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div
        x-data
        x-cloak
        @open-mood-modal.window="(e) => {
            $store.moodModal.open = true;
            $store.moodModal.mode = e.detail.mode;
            if (e.detail.mode === 'edit' && e.detail.mood) {
                $store.moodModal.mood = {
                    id: e.detail.mood.id,
                    mood_type: e.detail.mood.mood_label,
                    date: e.detail.mood.recorded_date
                };
            } else {
                $store.moodModal.mood = {
                    id: null,
                    mood_type: 'neutral',
                    date: '{{ now()->toDateString() }}'
                };
            }
        }"
    >
        <div x-show="$store.moodModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40" @click="$store.moodModal.open = false"></div>

            <div class="relative w-full max-w-2xl px-4">
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="$store.moodModal.mode === 'create' ? 'How are you feeling today?' : 'Update your mood'"></h3>
                            <button @click="$store.moodModal.open = false" class="text-slate-500">✕</button>
                        </div>

                        <form :action="$store.moodModal.mode === 'create' ? '{{ route('moods.store') }}' : '/moods/' + ($store.moodModal.mood.id ?? '')" method="POST" class="mt-5 space-y-5">
                            @csrf
                            <template x-if="$store.moodModal.mode === 'edit'">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>

                            <div>
                                <label class="form-label mb-3">Select Mood</label>
                                <div class="grid grid-cols-3 gap-2 sm:grid-cols-5">
                                    @foreach ($moodTypes as $type)
                                        <button
                                            type="button"
                                            @click="$store.moodModal.mood.mood_type = '{{ $type }}'"
                                            :class="$store.moodModal.mood.mood_type === '{{ $type }}' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-700'"
                                            class="rounded-xl border px-3 py-3 text-center text-sm transition"
                                        >
                                            <div class="text-xl">{{ $emojiMap[$type] ?? '🙂' }}</div>
                                            <div class="mt-1 text-xs font-medium">{{ ucfirst($type) }}</div>
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="mood_type" :value="$store.moodModal.mood.mood_type">
                            </div>

                            <div>
                                <label class="form-label">Date</label>
                                <input type="date" name="date" x-model="$store.moodModal.mood.date" class="form-input w-full" required>
                                <p class="mt-1 text-xs text-slate-500">Only one mood entry is allowed per day.</p>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="$store.moodModal.open = false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary" x-text="$store.moodModal.mode === 'create' ? 'Save Mood' : 'Update Mood'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('moodModal', {
                open: false,
                mode: 'create',
                mood: {
                    id: null,
                    mood_type: 'neutral',
                    date: '{{ now()->toDateString() }}',
                },
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.initMoodTrackerWeeklyChart) {
                window.initMoodTrackerWeeklyChart(@json($weeklyChartData));
            }
        });
    </script>
</x-app-layout>
