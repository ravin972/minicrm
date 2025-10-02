<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
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
            'source' => 'required|in:website,referral,social_media,email,phone,other',
            'customer_id' => 'required|exists:customers,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:new,contacted,qualified,proposal,won,lost',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Lead title is required.',
            'source.required' => 'Lead source is required.',
            'source.in' => 'Please select a valid lead source.',
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'assigned_to.required' => 'Assigned user is required.',
            'assigned_to.exists' => 'Selected user does not exist.',
            'status.required' => 'Lead status is required.',
            'status.in' => 'Please select a valid lead status.',
        ];
    }
}
