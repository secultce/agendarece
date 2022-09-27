<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgrammation extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'schedule'    => 'required|integer',
            'users'       => 'sometimes|array',
            'spaces'      => 'required|array|min:1',
            'category'    => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'start_time'  => 'required|string',
            'end_time'    => 'required|string',
            'start_date'  => 'required|string',
            'end_date'    => 'nullable|string'
        ];
    }
}
