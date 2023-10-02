<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgrammation extends FormRequest
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
            $scheduleUsers->contains('id', auth()->user()->id)
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
            'end_date'        => 'nullable|string',
            'loop_days'       => ['array', Rule::requiredIf(empty($this->end_date))],
            'accessibilities' => 'nullable|array',
            'remind_at'       => 'nullable|integer|min:1|max:15',
            'has_reminder'    => 'required'
        ];
    }
}
