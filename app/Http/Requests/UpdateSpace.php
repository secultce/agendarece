<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'   => ['required', 'string', Rule::unique('spaces')->ignore($this->space->id, 'id')],
            'active' => 'required'
        ];
    }
}
