<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgrammation extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users'       => 'required|array|min:1',
            'space'       => 'required|integer',
            'category'    => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string|max:255',
            'start_time'  => 'required|string',
            'end_time'    => 'required|string',
            'start_date'  => 'required|string',
            'end_date'    => 'nullable|string'
        ];
    }
}
