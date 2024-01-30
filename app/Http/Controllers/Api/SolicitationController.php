<?php

namespace App\Http\Controllers\Api;

use App\Events\NotifyResponsibles;
use App\Http\Controllers\Controller;
use App\Models\Programmation;
use App\Models\Solicitation;
use App\Models\SolicitationSpace;
use App\Http\Requests\StoreSolicitation;
use App\Models\Log;

class SolicitationController extends Controller
{
    private function exists($data, $model)
    {
        $query = $model::where('schedule_id', $data['schedule']['id'])
            ->whereHas('spaces', fn ($query) => $query->whereIn('space_id', $data['spaces']))
            ->whereRaw("((start_time between ? and ? or end_time between ? and ?) or (? between start_time and end_time or ? between start_time and end_time))", [
                $data['start_time'],
                $data['end_time'],
                $data['start_time'],
                $data['end_time'],
                $data['start_time'],
                $data['end_time']
            ])->whereRaw('((end_date is null and ? >= start_date and (find_in_set(date_format(?, "%w"), loop_days) > 0 or find_in_set(date_format(?, "%w"), loop_days) > 0)) or (start_date between ? and ? or end_date between ? and ?) or (? between start_date and end_date or ? between start_date and end_date))', [
                $data['start_date'],
                $data['start_date'],
                $data['end_date'],
                $data['start_date'],
                $data['end_date'],
                $data['start_date'],
                $data['end_date'],
                $data['start_date'],
                $data['end_date']
            ])
        ;

        return $query->first();
    }

    public function store(StoreSolicitation $request)
    {
        $data = $request->validated();
        $spaceGroup = [];

        if ($exists = $this->exists($data, Solicitation::class)) return abort(403, __('Already exists a solicitation for this period and space created by') . " {$exists->user->name}");
        if ($exists = $this->exists($data, Programmation::class)) return abort(403, __('Already exists a programmation for this period and space created by') . " {$exists->user->name}");

        $solicitation = Solicitation::create([
            'user_id'         => auth()->user()->id,
            'occupation_id'   => $data['occupation'],
            'schedule_id'     => $data['schedule']['id'],
            'category_id'     => $data['category'],
            'title'           => $data['title'],
            'description'     => $data['description'],
            'parental_rating' => $data['parental_rating'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'loop_days'       => '',
            'accessibilities' => implode(',', $data['accessibilities']),
        ]);

        foreach ($data['spaces'] as $space) $spaceGroup[] = new SolicitationSpace(['solicitation_id' => $solicitation->id, 'space_id' => $space]);

        $solicitation->spaces()->saveMany($spaceGroup);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou uma solicitação de programação chamada " . $data['title']
        ]);

        NotifyResponsibles::dispatch($solicitation);

        return response()->json([
            'message' => __('Solicitation created successfully')
        ], 200);
    }
}
