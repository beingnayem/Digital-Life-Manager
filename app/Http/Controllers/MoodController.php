<?php

namespace App\Http\Controllers;

use App\Models\Mood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $moods = $request->user()
            ->moods()
            ->latest('recorded_date')
            ->paginate(10);

        return view('moods.index', compact('moods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('moods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mood_level' => ['required', 'integer', 'between:1,10'],
            'mood_label' => ['nullable', 'string', 'max:50'],
            'energy_level' => ['nullable', 'integer', 'between:1,10'],
            'stress_level' => ['nullable', 'integer', 'between:1,10'],
            'focus_level' => ['nullable', 'integer', 'between:1,10'],
            'emotion_tags' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'activities' => ['nullable', 'array'],
            'sleep_hours' => ['nullable', 'numeric', 'between:0,24'],
            'weather' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'recorded_date' => ['required', 'date'],
            'recorded_at' => ['required', 'date'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Mood::create($validated);

        return Redirect::route('moods.index')->with('status', 'mood-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mood $mood): View
    {
        return view('moods.edit', compact('mood'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mood $mood): RedirectResponse
    {
        $validated = $request->validate([
            'mood_level' => ['required', 'integer', 'between:1,10'],
            'mood_label' => ['nullable', 'string', 'max:50'],
            'energy_level' => ['nullable', 'integer', 'between:1,10'],
            'stress_level' => ['nullable', 'integer', 'between:1,10'],
            'focus_level' => ['nullable', 'integer', 'between:1,10'],
            'emotion_tags' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'activities' => ['nullable', 'array'],
            'sleep_hours' => ['nullable', 'numeric', 'between:0,24'],
            'weather' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'recorded_date' => ['required', 'date'],
            'recorded_at' => ['required', 'date'],
        ]);

        $mood->update($validated);

        return Redirect::route('moods.index')->with('status', 'mood-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mood $mood): RedirectResponse
    {
        $mood->delete();

        return Redirect::route('moods.index')->with('status', 'mood-deleted');
    }
}