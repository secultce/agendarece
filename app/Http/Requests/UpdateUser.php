<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role'               => 'required|integer|exists:roles,id',
            'name'               => 'required|string',
            'email'              => ['required', 'string', 'email', Rule::unique('users')->ignore($this->user->id)],
            'password'           => 'nullable|string|min:8|confirmed',
            'active'             => 'required'
        ];
    }
}
