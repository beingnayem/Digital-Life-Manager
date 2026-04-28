<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
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
        $query = $request->user()->notes()->active();

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by pin status
        if ($request->has('pinned') && $request->pinned === '1') {
            $query->where('is_pinned', true);
        }

        // Search by title or content
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $notes = $query->latest('updated_at')->paginate(12)->withQueryString();

        // Get distinct categories for filter dropdown
        $categories = $request->user()
            ->notes()
            ->active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        return view('notes.index', compact('notes', 'categories'));
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
    public function store(NoteRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $validated['is_pinned'] = false;
        $validated['is_archived'] = false;

        Note::create($validated);

        return Redirect::route('notes.index')->with('status', 'note-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note): View
    {
        // Verify ownership
        if ($note->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('notes.edit', compact('note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NoteRequest $request, Note $note): RedirectResponse
    {
        // Verify ownership
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        $note->update($validated);

        return Redirect::route('notes.index')->with('status', 'note-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note): RedirectResponse
    {
        // Verify ownership
        if ($note->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        return Redirect::route('notes.index')->with('status', 'note-deleted');
    }

    /**
     * Toggle pin status via POST (for quick actions)
     */
    public function togglePin(Note $note): RedirectResponse
    {
        // Verify ownership
        if ($note->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $note->update(['is_pinned' => !$note->is_pinned]);

        return Redirect::route('notes.index')->with('status', 'note-updated');
    }

    /**
     * Archive a note via POST
     */
    public function archive(Note $note): RedirectResponse
    {
        // Verify ownership
        if ($note->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $note->update(['is_archived' => true]);

        return Redirect::route('notes.index')->with('status', 'note-archived');
    }
}