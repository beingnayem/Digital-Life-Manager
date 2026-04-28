<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoodRequest;
use App\Models\Mood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MoodController extends Controller
{
    /**
     * Display a listing of the resource with lightweight filters.
     */
    public function index(Request $request): View
    {
        $query = $request->user()->moods();

        if ($request->filled('mood_type')) {
            $query->where('mood_label', $request->string('mood_type'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('recorded_date', '>=', $request->date('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('recorded_date', '<=', $request->date('end_date'));
        }

        $moods = $query->latest('recorded_date')->paginate(12)->withQueryString();

        $moodTypes = [
            'happy',
            'sad',
            'anxious',
            'angry',
            'calm',
            'excited',
            'tired',
            'neutral',
            'stressed',
        ];

        $weeklyChartData = $this->buildWeeklyChartData($request->user()->id);

        return view('moods.index', compact('moods', 'moodTypes', 'weeklyChartData'));
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
    public function store(MoodRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Mood::create([
            'user_id' => $request->user()->id,
            'mood_label' => $validated['mood_type'],
            'mood_level' => $this->mapMoodTypeToLevel($validated['mood_type']),
            'recorded_date' => $validated['date'],
            'recorded_at' => now(),
            'emotion_tags' => [$validated['mood_type']],
        ]);

        return Redirect::route('moods.index')->with('status', 'mood-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mood $mood): View
    {
        if ($mood->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('moods.edit', compact('mood'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MoodRequest $request, Mood $mood): RedirectResponse
    {
        if ($mood->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        $mood->update([
            'mood_label' => $validated['mood_type'],
            'mood_level' => $this->mapMoodTypeToLevel($validated['mood_type']),
            'recorded_date' => $validated['date'],
            'recorded_at' => now(),
            'emotion_tags' => [$validated['mood_type']],
        ]);

        return Redirect::route('moods.index')->with('status', 'mood-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mood $mood): RedirectResponse
    {
        if ($mood->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $mood->delete();

        return Redirect::route('moods.index')->with('status', 'mood-deleted');
    }

    /**
     * Map mood type labels to a normalized 1-10 scale.
     */
    private function mapMoodTypeToLevel(string $moodType): int
    {
        return match ($moodType) {
            'excited' => 9,
            'happy' => 8,
            'calm' => 7,
            'neutral' => 5,
            'tired' => 4,
            'sad' => 3,
            'anxious' => 3,
            'stressed' => 2,
            'angry' => 2,
            default => 5,
        };
    }

    /**
     * Build a 7-day mood chart payload.
     */
    private function buildWeeklyChartData(int $userId): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();

            $entry = Mood::query()
                ->where('user_id', $userId)
                ->whereDate('recorded_date', $date)
                ->first(['mood_level']);

            $data[] = [
                'label' => $date->format('D'),
                'value' => $entry?->mood_level,
            ];
        }

        return $data;
    }
}