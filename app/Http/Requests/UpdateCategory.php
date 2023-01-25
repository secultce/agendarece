<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'sector' => 'nullable',
            'name'   => ['required', 'string', Rule::unique('categories')->ignore($this->category->id, 'id')],
            'color'  => 'required|string'
        ];
    }
}
