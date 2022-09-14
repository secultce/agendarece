<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programmation;
use App\Models\ProgrammationUser;
use App\Http\Requests\StoreProgrammation;
use App\Http\Requests\UpdateProgrammation;

class ProgrammationController extends Controller
{
    private function exists($data, $programmation = null)
    {
        $query = Programmation::whereRaw("space_id = ? and (start_time between ? and ? or end_time between ? and ?)", [
            $data['space'],
            $data['start_time'],
            $data['end_time'],
            $data['start_time'],
            $data['end_time']
        ]);
        
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

    public function list()
    {
        return response()->json([
            'message' => __('Programmations listed successfully'),
            'data'    => Programmation::all()
        ], 200);
    }

    public function store(StoreProgrammation $request)
    {
        $data  = $request->validated();
        $group = [];

        if ($this->exists($data)) return abort(403, __('Already exists a programmation for this period and space'));

        $programmation = Programmation::create([
            'space_id'    => $data['space'],
            'category_id' => $data['category'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date']
        ]);

        foreach ($data['users'] as $user) $group[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

        $programmation->users()->delete();
        $programmation->users()->saveMany($group);

        return response()->json([
            'message' => __('Programmation created successfully')
        ], 200);
    }

    public function update(UpdateProgrammation $request, $programmation)
    {
        $data  = $request->validated();
        $group = [];

        if ($this->exists($data, $programmation)) return abort(403, __('Already exists a programmation for this period and space'));

        $programmation->space_id    = $data['space'];
        $programmation->category_id = $data['category'];
        $programmation->title       = $data['title'];
        $programmation->description = $data['description'];
        $programmation->start_time  = $data['start_time'];
        $programmation->end_time    = $data['end_time'];
        $programmation->start_date  = $data['start_date'];
        $programmation->end_date    = $data['end_date'];

        foreach ($data['users'] as $user) $group[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

        $programmation->save();
        $programmation->users()->delete();
        $programmation->users()->saveMany($group);

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
