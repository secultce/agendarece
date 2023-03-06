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
        $schedules = null;
        $role      = auth()->user()->role->tag;
        $sector    = $request->sector ? $request->sector->id : (auth()->user()->sector ? auth()->user()->sector->id : null);

        if (in_array($role, ['scheduler', 'responsible', 'user'])) {
            if ($role === 'user') {
                $schedules = Schedule::where('private', false);

                if ($sector) $schedules->where('sector_id', $sector);

                $schedules->orderBy('name');
            } else {
                $firstUnion = Schedule::where('private', false);
                $secondUnion = Schedule::where('private', true)->whereHas('programmations', function ($programmationQuery) {
                    $programmationQuery->whereHas('users', function ($userQuery) {
                        $userQuery->where('user_id', auth()->user()->id);
                    });
                });

                if ($sector) {
                    $firstUnion->where('sector_id', $sector);
                    $secondUnion->where('sector_id', $sector);

                    $schedules = auth()
                        ->user()
                        ->schedules()
                        ->where('sector_id', $sector)
                        ->union($firstUnion)
                        ->union($secondUnion)
                        ->orderBy('name')
                    ;
                } else {
                    $schedules = auth()
                        ->user()
                        ->schedules()
                        ->union($firstUnion)
                        ->union($secondUnion)
                        ->orderBy('name')
                    ;
                }
            }
        }
        
        if ($role === 'administrator') {
            $schedules = Schedule::orderBy('name');

            if ($sector) $schedules->where('sector_id', $sector);

            $schedules->orderBy('name');
        }

        return response()->json([
            'message' => __('Schedules listed successfully'),
            'data'    => $schedules->get()
        ], 200);
    }

    public function store(StoreSchedule $request)
    {
        $data = $request->validated();
        $schedule = Schedule::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'],
            'user_id'   => auth()->user()->id,
            'name'      => $data['name'],
            'private'   => $data['private']
        ]);

        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou uma agenda chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Schedule created successfully')
        ], 200);
    }

    public function update(UpdateSchedule $request, $schedule)
    {
        $data = $request->validated();

        $schedule->sector_id = auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'];
        $schedule->name      = $data['name'];
        $schedule->private   = $data['private'];

        $schedule->save();
        $schedule->users()->sync($data['users']);
        $schedule->shares()->sync($data['shares']);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
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
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu a agenda " . $schedule->name
        ]);

        $schedule->delete();

        return response()->json([
            'message' => __('Schedule removed successfully')
        ], 200);
    }
}
