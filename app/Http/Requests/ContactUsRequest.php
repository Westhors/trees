<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100|regex:/^[\pL\s]+$/u',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:2000',
            'type' => 'nullable|string|in:complaint,contact',
        ];
    }

}

