<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array(auth()->user()->role->tag, ['administrator', 'scheduler', 'responsible']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'occupation'      => 'nullable',
            'schedule'        => 'required|array',
            'users'           => 'sometimes|array',
            'spaces'          => 'required|array|min:1',
            'category'        => 'required|integer',
            'title'           => 'required|string',
            'description'     => 'nullable|string',
            'parental_rating' => 'required|integer',
            'start_time'      => 'required|string',
            'end_time'        => 'required|string',
            'start_date'      => 'required|string',
            'end_date'        => 'required|string',
            'accessibilities' => 'nullable|array',
        ];
    }
}
