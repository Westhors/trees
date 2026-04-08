<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMemberRequest extends FormRequest
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
            'type' => 'required|in:father,son',

            'name' => 'required|string|max:255',

            'gender' => 'required|in:male,female',

            'father_id' => 'nullable|exists:members,id',

            'mother_name' => 'nullable|string|max:255',
            'wife_name' => 'nullable|string|max:255',

            'phone' => [
                'nullable',
                'string',
                Rule::unique('members', 'phone'),
            ],

            'national_id' => [
                'nullable',
                'string',
                Rule::unique('members', 'national_id'),
            ],

            'date_of_birth' => 'nullable|date',
            'date_of_death' => 'nullable|date',

            'dead' => 'nullable|boolean',

            'password' => 'nullable|string|min:6',
        ];
    }

}

