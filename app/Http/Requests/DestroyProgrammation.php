<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyProgrammation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $schedule = $this->programmation->schedule;
        $scheduleUsers = collect($schedule['users']);

        return auth()->user()->role->tag === 'administrator' || 
            $schedule['user_id'] === auth()->user()->id ||
            $scheduleUsers->contains('id', auth()->user()->id) ||
            $scheduleUsers->isEmpty()
        ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 
        ];
    }
}
