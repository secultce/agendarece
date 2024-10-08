<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomHoliday extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sector'   => 'nullable',
            'name'     => 'required|string',
            'body'     => 'nullable',
            'start_at' => 'required|string',
            'end_at'   => 'required|string'
        ];
    }
}
