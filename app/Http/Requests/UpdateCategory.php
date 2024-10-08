<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategory extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'axis'   => 'nullable',
            'sector' => 'nullable',
            'name'   => ['required', 'string'],
            'color'  => 'required|string'
        ];
    }
}
