<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgrammationLink extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role->tag === 'administrator' || ($this->link->user_id === auth()->user()->id && $this->programmation->users->pluck('user_id')->contains(auth()->user()->id));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'url'  => 'required|string'
        ];
    }
}
