<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Edit Note</p>
            </div>
            <a href="{{ route('notes.index') }}" class="btn-secondary">Back to Notes</a>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="card mx-auto max-w-3xl">
                <div class="card-body">
                    <div class="mb-6 flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm text-slate-500">Update the note and keep your workspace organized.</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $note->title }}</h2>
                        </div>
                        <a href="{{ route('dashboard') }}" class="link text-sm">Dashboard</a>
                    </div>

                    <form method="POST" action="{{ route('notes.update', $note) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="title">Title</label>
                                <input id="title" name="title" type="text" value="{{ old('title', $note->title) }}" class="form-input w-full" required />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="category">Category</label>
                                <input id="category" name="category" type="text" value="{{ old('category', $note->category) }}" class="form-input w-full" placeholder="Work, Personal, Ideas" />
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="content">Content</label>
                            <textarea id="content" name="content" class="form-input w-full" rows="10" required>{{ old('content', $note->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a href="{{ route('notes.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">Save Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
