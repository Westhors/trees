<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'gender' => 'required|in:male,female',
            'father_id' => 'nullable|exists:members,id',
            'mother_name' => 'nullable|string|max:255',
            'wife_name' => 'nullable|string|max:255',
            'phone' => [
                'nullable',
                'string',
                Rule::unique('members', 'phone')->ignore($this->member?->id),
            ],
            'national_id' => [
                'nullable',
                'string',
                Rule::unique('members', 'national_id')->ignore($this->member?->id),
            ],
            'date_of_birth' => 'nullable|date',
            'city' => 'nullable|string',
            'password' => $this->isMethod('post') ? 'required|string|min:6' : 'nullable|string|min:6',
        ];
    }

}

