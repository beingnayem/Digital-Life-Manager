<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare payload to support both new and legacy field names.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'mood_type' => $this->input('mood_type', $this->input('mood_label')),
            'date' => $this->input('date', $this->input('recorded_date')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $moodId = $this->route('mood')?->id;

        return [
            'mood_type' => ['required', 'string', Rule::in([
                'happy',
                'sad',
                'anxious',
                'angry',
                'calm',
                'excited',
                'tired',
                'neutral',
                'stressed',
            ])],
            'date' => [
                'required',
                'date',
                Rule::unique('moods', 'recorded_date')
                    ->where(fn ($query) => $query->where('user_id', $this->user()?->id))
                    ->ignore($moodId),
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'mood_type.required' => 'Mood type is required.',
            'mood_type.in' => 'Please choose a valid mood type.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be valid.',
            'date.unique' => 'You already recorded a mood for this day.',
        ];
    }
}
