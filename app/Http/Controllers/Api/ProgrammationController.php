<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programmation;
use App\Models\ProgrammationSpace;
use App\Models\ProgrammationUser;
use App\Http\Requests\StoreProgrammation;
use App\Http\Requests\UpdateProgrammation;
use App\Http\Requests\UpdateProgrammationDate;

class ProgrammationController extends Controller
{
    private function exists($data, $programmation = null)
    {
        $query = Programmation::whereHas('spaces', fn ($query) => $query->whereIn('space_id', $data['spaces']))
            ->whereRaw("(start_time between ? and ? or end_time between ? and ?)", [
                $data['start_time'],
                $data['end_time'],
                $data['start_time'],
                $data['end_time']
            ])
        ;
        
        if (isset($data['end_date'])) {
            $query->whereRaw('(start_date between ? and ? or end_date between ? and ?)', [
                $data['start_date'],
                $data['end_date'],
                $data['start_date'],
                $data['end_date']
            ]);
        } else {
            $query->whereRaw('(start_date >= ? or (start_date between start_date and ?))', [
                $data['start_date'],
                $data['start_date']
            ]);
        }

        if ($programmation) $query->where('id', '<>', $programmation->id);

        return $query->exists();
    }

    public function list(Request $request)
    {
        $programmations = [];

        if ($request->type === 'calendar') {
            $programmations = Programmation::whereRaw("extract(year_month from ?) BETWEEN extract(year_month from start_date) AND extract(year_month from end_date)", [$request->date])
                ->whereHas('users', function ($query) {
                    if (auth()->user()->role->tag === 'scheduler') $query->where('user_id', auth()->user()->id);
                })
                ->union(
                    Programmation::whereRaw("end_date is null and (extract(year_month from start_date) >= extract(year_month from ?) or extract(year_month from start_date) BETWEEN extract(year_month from start_date) and extract(year_month from ?))", [
                        $request->date,
                        $request->date
                    ])
                )->get()
            ;
        }

        if ($request->type === 'list') {

        }

        if ($request->type === 'day') {

        }

        return response()->json([
            'message' => __('Programmations listed successfully'),
            'data'    => $programmations
        ], 200);
    }

    public function store(StoreProgrammation $request)
    {
        $data = $request->validated();
        $spaceGroup = [];
        $userGroup  = [];

        if ($this->exists($data)) return abort(403, __('Already exists a programmation for this period and space'));

        $programmation = Programmation::create([
            'category_id' => $data['category'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date']
        ]);

        foreach ($data['spaces'] as $space) $spaceGroup[] = new ProgrammationSpace(['programmation_id' => $programmation->id, 'space_id' => $space]);
        foreach ($data['users'] as $user) $userGroup[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

        $programmation->spaces()->saveMany($spaceGroup);
        $programmation->users()->saveMany($userGroup);

        return response()->json([
            'message' => __('Programmation created successfully')
        ], 200);
    }

    public function update(UpdateProgrammation $request, $programmation)
    {
        $data = $request->validated();

        $spaceGroup = [];
        $userGroup  = [];

        if ($this->exists($data, $programmation)) return abort(403, __('Already exists a programmation for this period and space'));

        $programmation->category_id = $data['category'];
        $programmation->title       = $data['title'];
        $programmation->description = $data['description'];
        $programmation->start_time  = $data['start_time'];
        $programmation->end_time    = $data['end_time'];
        $programmation->start_date  = $data['start_date'];
        $programmation->end_date    = $data['end_date'];

        foreach ($data['spaces'] as $space) $spaceGroup[] = new ProgrammationSpace(['programmation_id' => $programmation->id, 'space_id' => $space]);
        foreach ($data['users'] as $user) $userGroup[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

        $programmation->save();
        $programmation->spaces()->delete();
        $programmation->users()->delete();
        $programmation->spaces()->saveMany($spaceGroup);
        $programmation->users()->saveMany($userGroup);

        return response()->json([
            'message' => __('Programmation updated successfully')
        ], 200);
    }

    public function updateDates(UpdateProgrammationDate $request, $programmation)
    {
        $data = $request->validated();

        if ($this->exists($data, $programmation)) return abort(403, __('Already exists a programmation for this period and space'));

        $programmation->start_date = $data['start_date'];
        $programmation->end_date   = $data['end_date'];

        $programmation->save();

        return response()->json([
            'message' => __('Programmation updated successfully')
        ], 200);
    }

    public function destroy($programmation)
    {
        $programmation->delete();

        return response()->json([
            'message' => __('Programmation removed successfully')
        ], 200);
    }
}
