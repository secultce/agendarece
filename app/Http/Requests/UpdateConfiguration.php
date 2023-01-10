<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguration extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo'      => 'nullable|mimes:jpg',
            'contact'   => 'nullable|email',
            'copyright' => 'nullable'
        ];
    }
}
