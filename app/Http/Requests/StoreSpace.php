<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpace extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sector' => 'nullable',
            'icon'   => 'required|image|mimes:svg',
            'name'   => 'required|string|unique:spaces',
            'active' => 'required'
        ];
    }
}
