<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Knowledge</p>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">Notes</h1>
            </div>
            <button x-data @click="$dispatch('open-note-modal', { mode: 'create' })" class="btn-primary">+ New Note</button>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <!-- Filters -->
            <div class="card mb-6">
                <div class="card-body">
                    <p class="mb-4 text-sm font-semibold text-slate-900">Filters</p>
                    <form method="GET" action="{{ route('notes.index') }}" class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                        <div>
                            <label class="form-label">Search</label>
                            <input name="search" value="{{ request('search') }}" placeholder="Search title or content..." class="form-input w-full text-sm" />
                        </div>

                        <div>
                            <label class="form-label">Category</label>
                            <select name="category" class="form-input w-full text-sm">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <label class="form-label block">Show Pinned</label>
                            <input name="pinned" type="checkbox" value="1" {{ request('pinned') === '1' ? 'checked' : '' }} class="h-5 w-5" />
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-secondary">Filter</button>
                            <a href="{{ route('notes.index') }}" class="btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notes Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($notes as $note)
                <div class="card group">
                    <div class="card-body">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium uppercase text-slate-500">{{ $note->category ? ucfirst($note->category) : 'General' }}</p>
                                <h3 class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $note->title }}</h3>
                            </div>
                            <form method="POST" action="{{ route('notes.togglePin', $note) }}" class="ml-2">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-yellow-500" title="{{ $note->is_pinned ? 'Unpin' : 'Pin' }}">
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

                        <p class="mb-4 line-clamp-3 text-sm text-slate-600">{{ Str::limit($note->content, 150) }}</p>

                        <div class="flex items-center justify-between gap-2 text-xs text-slate-500">
                            <span>{{ $note->updated_at->diffForHumans() }}</span>
                            <div class="flex gap-2">
                                <button x-data @click="$dispatch('open-note-modal', { mode: 'edit', note: {{ json_encode($note) }} })" class="text-primary-600 hover:text-primary-700">Edit</button>
                                <form method="POST" action="{{ route('notes.destroy', $note) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Delete this note?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="card">
                        <div class="card-body py-12 text-center">
                            <p class="text-slate-500">No notes found. <a href="{{ route('notes.index') }}" class="text-primary-600 hover:underline">Create one</a>.</p>
                        </div>
                    </div>
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
                                <button type="submit" class="btn-primary" x-text="$store.noteModal.mode === 'create' ? 'Create' : 'Save'"></button>
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
