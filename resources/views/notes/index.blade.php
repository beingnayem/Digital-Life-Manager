<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Knowledge</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Notes</h1>
            </div>
            <button x-data @click="$dispatch('open-note-modal', { mode: 'create' })" class="btn-primary">+ Add Note</button>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="note-toolbar">
                <div class="note-toolbar-inner">
                    <form method="GET" action="{{ route('notes.index') }}" class="flex w-full flex-col gap-3 lg:flex-row lg:items-center">
                        <input name="search" value="{{ request('search') }}" placeholder="Search notes..." class="note-search text-sm" />

                        <div class="flex flex-wrap items-center gap-2">
                            <select name="category" class="form-input rounded-xl border-slate-200 text-sm">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600">
                                <input name="pinned" type="checkbox" value="1" {{ request('pinned') === '1' ? 'checked' : '' }} class="form-checkbox" />
                                Pinned
                            </label>

                            <button type="submit" class="btn-secondary">Apply</button>
                            <a href="{{ route('notes.index') }}" class="btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="note-grid">
                @forelse ($notes as $note)
                <article class="note-card group">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $note->category ? ucfirst($note->category) : 'General' }}</p>
                                <h3 class="note-card-title mt-1 truncate">{{ $note->title }}</h3>
                            </div>
                            <form method="POST" action="{{ route('notes.togglePin', $note) }}" class="ml-2">
                                @csrf
                                <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-yellow-500" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
                                    @if ($note->is_pinned)
                                        <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <p class="note-card-content line-clamp-4">{{ Str::limit($note->content, 220) }}</p>

                        <div class="mt-5 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500">
                            <span>{{ $note->updated_at->diffForHumans() }}</span>
                            <div class="flex gap-2">
                                <button x-data @click="$dispatch('open-note-modal', { mode: 'edit', note: {{ json_encode($note) }} })" class="rounded-md px-2 py-1 text-primary-600 transition hover:bg-primary-50 hover:text-primary-700">Edit</button>
                                <form method="POST" action="{{ route('notes.destroy', $note) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md px-2 py-1 text-red-600 transition hover:bg-red-50 hover:text-red-700" onclick="return confirm('Delete this note?')">Delete</button>
                                </form>
                            </div>
                        </div>
                </article>
                @empty
                <div class="col-span-full">
                    <x-empty-state
                        title="No notes yet"
                        description="Create your first note or adjust filters to reveal saved knowledge and ideas."
                    >
                        <button x-data @click="$dispatch('open-note-modal', { mode: 'create' })" class="btn-primary">+ Add Note</button>
                    </x-empty-state>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($notes->hasPages())
                <div class="mt-6">{{ $notes->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Note Modal (create/edit) --}}
    <div x-data x-cloak @open-note-modal.window="(e) => { $store.noteModal.open = true; $store.noteModal.mode = e.detail.mode; if(e.detail.mode === 'edit') { $store.noteModal.note = e.detail.note; } else { $store.noteModal.note = { title: '', content: '', category: '' }; } }">
        <div x-show="$store.noteModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40" @click="$store.noteModal.open=false"></div>
            <div class="relative w-full max-w-3xl">
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="$store.noteModal.mode === 'create' ? 'Create Note' : 'Edit Note'"></h3>
                            <button @click="$store.noteModal.open=false" class="text-slate-500">✕</button>
                        </div>

                        <form :action="$store.noteModal.mode === 'create' ? '{{ route('notes.store') }}' : '/notes/' + ($store.noteModal.note.id ?? '')" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <template x-if="$store.noteModal.mode === 'edit'">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="form-label">Title</label>
                                    <input name="title" x-model="$store.noteModal.note.title" class="form-input w-full" placeholder="Note title..." required />
                                </div>

                                <div>
                                    <label class="form-label">Category</label>
                                    <input name="category" x-model="$store.noteModal.note.category" class="form-input w-full" placeholder="e.g., Work, Personal, Ideas" />
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Content</label>
                                <textarea name="content" x-model="$store.noteModal.note.content" class="form-input w-full" rows="8" placeholder="Write your note here..." required></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="$store.noteModal.open=false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary" data-loading-label="Saving..." x-text="$store.noteModal.mode === 'create' ? 'Create' : 'Save'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('noteModal', {
                open: false,
                mode: 'create',
                note: { }
            });
        });
    </script>
</x-app-layout>
