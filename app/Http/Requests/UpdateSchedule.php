<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchedule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role->tag === 'administrator' || $this->schedule->user_id === auth()->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sector'  => 'nullable',
            'shares'  => 'sometimes|array',
            'users'   => 'sometimes|array',
            'name'    => 'required|string',
            'private' => 'required',
            'calendar_icons' => 'required',
        ];
    }
}
