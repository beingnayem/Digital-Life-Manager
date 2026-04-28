<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $notes = $request->user()
            ->notes()
            ->latest()
            ->paginate(10);

        return view('notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('notes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'color_tag' => ['nullable', 'string', 'size:7'],
            'is_pinned' => ['sometimes', 'boolean'],
            'is_archived' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
            'collaborator_ids' => ['nullable', 'array'],
            'permission_level' => ['required', 'in:private,shared,public'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Note::create($validated);

        return Redirect::route('notes.index')->with('status', 'note-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note): View
    {
        return view('notes.edit', compact('note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'color_tag' => ['nullable', 'string', 'size:7'],
            'is_pinned' => ['sometimes', 'boolean'],
            'is_archived' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
            'collaborator_ids' => ['nullable', 'array'],
            'permission_level' => ['required', 'in:private,shared,public'],
        ]);

        $note->update($validated);

        return Redirect::route('notes.index')->with('status', 'note-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return Redirect::route('notes.index')->with('status', 'note-deleted');
    }
}