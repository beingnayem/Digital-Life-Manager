<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
            'payment_method' => ['nullable', 'in:cash,card,check,bank_transfer,mobile_payment,other'],
            'status' => ['nullable', 'in:pending,confirmed,disputed,refunded'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.gt' => 'Amount must be greater than 0.',
            'category.required' => 'Category is required.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'payment_method' => $this->payment_method ?? 'card',
            'status' => $this->status ?? 'confirmed',
        ]);
    }
}
