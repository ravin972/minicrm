<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lead_id' => 'required|exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'lead_id.required' => 'Lead is required.',
            'lead_id.exists' => 'Selected lead does not exist.',
            'assigned_to.required' => 'Assigned user is required.',
            'assigned_to.exists' => 'Selected user does not exist.',
            'status.required' => 'Task status is required.',
            'status.in' => 'Please select a valid task status.',
            'due_date.date' => 'Please enter a valid due date.',
        ];
    }
}
