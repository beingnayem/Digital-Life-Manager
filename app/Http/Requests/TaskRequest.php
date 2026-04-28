<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:not_started,in_progress,completed,archived,cancelled'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'integer', 'min:0'],
            'actual_hours' => ['nullable', 'integer', 'min:0'],
            'color_tag' => ['nullable', 'string', 'size:7'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurrence_pattern' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
        ];
    }
}
