<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $role = $this->input('role', 'user');

        if ($this->isMethod('post')) {
            $passwordRule = 'required|min:6';
        } else {
            $passwordRule = 'nullable|min:6';
        }

        // ENGINEER
        if ($role === 'engineer') {
            return [
                'name'     => 'required|string|max:255',
'phone' => [
    'required',
    'string',
    Rule::unique('users', 'phone')->ignore($this->user?->id),
],                'password' => $passwordRule,
                'role' => 'required|in:user,engineer,seller,merchant,company'
            ];
        }

        // SELLER
        if ($role === 'seller') {
            return [
                'name'     => 'required|string|max:255',
'phone' => [
    'required',
    'string',
    Rule::unique('users', 'phone')->ignore($this->user?->id),
],                'password' => $passwordRule,
                'address'  => 'required|string|max:255',
                'city'     => 'required|string|max:255',
                'state'    => 'required|string|max:255',
                'nature_of_work' => 'required|string|max:255',
                'workshop_name'  => 'required|string|max:255',
                'role' => 'required|in:user,engineer,seller,merchant,company'
            ];
        }

        // MERCHANT
        if ($role === 'merchant') {
            return [
                'name'     => 'required|string|max:255',
                'phone'           => [
                            'required',
                            'string',
                            Rule::unique('users', 'phone')->ignore($this->user?->id),
                        ],
                'password' => $passwordRule,
                'facebook_link'   => 'required|string|max:255',
                'whatsapp_number' => 'required|string|max:20',
                'role' => 'required|in:user,engineer,seller,merchant,company'
            ];
        }




        // USER
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($this->user?->id),
            ],
            'password' => $passwordRule,
            'role' => 'required|in:user,engineer,seller,merchant,company'
        ];
    }


}



