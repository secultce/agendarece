<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Schedule;
use App\Http\Requests\StoreSchedule;
use App\Http\Requests\UpdateSchedule;
use App\Http\Requests\ToggleSchedule;

class ScheduleController extends Controller
{
    public function list(Request $request)
    {
        $schedules = [];
        
        $role      = auth()->user()->role->tag;

        if (in_array($role, ['scheduler', 'responsible', 'user'])) {
            $sector    = $request->sector->id ?? auth()->user()->sector->id;

            if ($role === 'user') {
                $schedules = Schedule::where('sector_id', $request->sector->id)->where('private', false)->get();
            } else {
                $schedules = auth()
                    ->user()
                    ->schedules()
                    ->union(Schedule::where('sector_id', $sector)->where('private', false))
                    ->union(Schedule::where('sector_id', $sector)->where('private', true)->whereHas('programmations', function ($programmationQuery) {
                        $programmationQuery->whereHas('users', function ($userQuery) {
                            $userQuery->where('user_id', auth()->user()->id);
                        });
                    }))
                    ->get()
                ;
            }
        }
        
        if ($role === 'administrator') $schedules = Schedule::all();

        return response()->json([
            'message' => __('Schedules listed successfully'),
            'data'    => $schedules
        ], 200);
    }

    public function store(StoreSchedule $request)
    {
        $data = $request->validated();
        $schedule = Schedule::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? auth()->user()->sector->id : $data['sector'],
            'user_id'   => auth()->user()->id,
            'name'      => $data['name'],
            'private'   => $data['private']
        ]);

        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Criou uma agenda chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Schedule created successfully')
        ], 200);
    }

    public function update(UpdateSchedule $request, $schedule)
    {
        $data = $request->validated();

        $schedule->sector_id = auth()->user()->role->tag !== 'administrator' ? auth()->user()->sector->id : $data['sector'];
        $schedule->name      = $data['name'];
        $schedule->private   = $data['private'];

        $schedule->save();
        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Editou a agenda " . $schedule->name
        ]);

        return response()->json([
            'message' => __('Schedule updated successfully')
        ], 200);
    }

    public function toggleStatus(ToggleSchedule $request, $schedule)
    {
        $schedule->private = !$schedule->private;

        $schedule->save();

        return response()->json([
            'message' => __('Schedule updated successfully')
        ], 200);
    }

    public function destroy($schedule)
    {
        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Removeu a agenda " . $schedule->name
        ]);

        $schedule->delete();

        return response()->json([
            'message' => __('Schedule removed successfully')
        ], 200);
    }
}
