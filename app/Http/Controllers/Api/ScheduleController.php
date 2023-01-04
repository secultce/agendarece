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
    public function list()
    {
        $schedules = [];
        $role      = auth()->user()->role->tag;

        if ($role === 'scheduler') {
            $schedules = auth()
                ->user()
                ->schedules()
                ->union(Schedule::where('private', false))
                ->union(Schedule::whereHas('programmations', function ($programmationQuery) {
                    $programmationQuery->whereHas('users', function ($userQuery) {
                        $userQuery->where('user_id', auth()->user()->id);
                    });
                }))
                ->get()
            ;
        }
        
        if ($role === 'user') $schedules = Schedule::where('private', false)->get();
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
            'user_id' => auth()->user()->id,
            'name'    => $data['name'],
            'private' => $data['private']
        ]);

        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Criou uma agenda chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Schedule created successfully')
        ], 200);
    }

    public function update(UpdateSchedule $request, $schedule)
    {
        $data = $request->validated();

        $schedule->name    = $data['name'];
        $schedule->private = $data['private'];

        $schedule->save();
        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Editou a agenda " . $schedule->name
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
            'user' => auth()->user()->name,
            'action' => "Removeu a agenda " . $schedule->name
        ]);

        $schedule->delete();

        return response()->json([
            'message' => __('Schedule removed successfully')
        ], 200);
    }
}
