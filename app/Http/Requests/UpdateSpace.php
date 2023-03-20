<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpace extends FormRequest
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
            'icon'   => 'required_if:id,null|nullable|image|mimes:svg',
            'name'   => ['required', 'string'],
            'active' => 'required'
        ];
    }
}
