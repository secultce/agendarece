<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role->tag === 'administrator' ||
            (auth()->user()->role->tag === 'responsible' && $this->user->sector_id === auth()->user()->sector->id)
        ;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sector'   => 'nullable|integer',
            'role'     => 'required|integer|exists:roles,id',
            'name'     => 'required|string',
            'email'    => ['required', 'string', 'email', Rule::unique('users')->ignore($this->user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'active'   => 'required'
        ];
    }
}
