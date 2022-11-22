<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchedule extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users'   => 'sometimes|array',
            'name'    => 'required|string',
            'private' => 'required'
        ];
    }
}
