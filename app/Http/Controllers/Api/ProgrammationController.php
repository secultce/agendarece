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
use App\Http\Requests\DestroyProgrammation;
use App\Models\Log;
use App\Events\NotifyUsers;

class ProgrammationController extends Controller
{
    private function exists($data, $programmation = null)
    {
        $query = Programmation::where('schedule_id', $data['schedule'])
            ->whereHas('spaces', fn ($query) => $query->whereIn('space_id', $data['spaces']))
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

        return $query->first();
    }

    private function buildNotificationActions($programmation, $data = null, $destroy = false)
    {
        $actions = [];

        if ($programmation->wasRecentlyCreated) $actions["created"] = [];
        else if ($destroy) {
            $actions["destroyed"] = (object) [
                "title"       => $programmation->title,
                "start_date"  => $programmation->start_date,
                "end_date"    => $programmation->end_date,
                "start_time"  => $programmation->start_time,
                "end_time"    => $programmation->end_time,
                "user"        => $programmation->user,
                "schedule"    => $programmation->schedule,
                "category"    => $programmation->category,
                'spaces'      => $programmation->spaces->pluck('space'),
                'users'       => $programmation->users->pluck('user')
            ];
        } else {
            $startTime = $data['start_time'];
            $endTime   = $data['end_time'];

            if (strlen($startTime) === 5 || strlen($endTime) === 5) {
                $startTime = "{$data['start_time']}:00";
                $endTime   = "{$data['end_time']}:00";
            }

            if ($programmation->start_time !== $startTime || $programmation->end_time !== $endTime) {
                $actions["time_updated"] = (object) [
                    "start_time" => $programmation->start_time,
                    "end_time"   => $programmation->end_time
                ];
            }
    
            if ($programmation->start_date !== $data['start_date'] || $programmation->end_date !== $data['end_date']) {
                $actions["date_updated"] = (object) [
                    "start_date" => $programmation->start_date,
                    "end_date"   => $programmation->end_date
                ];
            }
    
            if (!collect($data['spaces'])->diff($programmation->spaces->pluck('space_id'))->isEmpty() 
                || !$programmation->spaces->pluck('space_id')->diff($data['spaces'])->isEmpty()
            ) {
                $actions["spaces_updated"] = (object) ['spaces' => $programmation->spaces->pluck('space')];
            }
            
            if (isset($data['users'])) {
                if (!collect($data['users'])->diff($programmation->users->pluck('user_id'))->isEmpty() ||
                    !$programmation->users->pluck('user_id')->diff($data['users'])->isEmpty()
                ) {
                    $actions["users_updated"] = (object) ['users' => $programmation->users->pluck('user')];
                }
            }

            if (isset($data['title']) && $programmation->title !== $data['title']) $actions["title_updated"] = (object) ["title" => $programmation->title];
            if (isset($data['category']) && $programmation->category_id !== $data['category']) $actions["category_updated"] = (object) ["category" => $programmation->category];
        }

        return $actions;
    }

    private function dispatchNotifications($actions, $programmation = null)
    {
        if (!$actions) return;

        $user = auth()->user();

        foreach ($actions as $action => $oldData) NotifyUsers::dispatch($user, $action, $programmation, $oldData);
    }

    public function list(Request $request)
    {
        $programmations = Programmation::where('schedule_id', $request->schedule);

        if (auth()->user()->role->tag === 'scheduler') {
            $programmations
                ->where(function ($query) {
                    $query
                        ->where('user_id', auth()->user()->id)
                        ->orWhereHas('users', function ($query) {
                            $query->where('user_id', auth()->user()->id);
                        })
                    ;
                })
            ;
        }

        if ($request->type === 'calendar') {
            $programmations->whereRaw("extract(year_month from ?) BETWEEN extract(year_month from start_date) AND extract(year_month from end_date)", [$request->date])
                ->union(
                    Programmation::whereRaw("end_date is null and (extract(year_month from start_date) >= extract(year_month from ?) or extract(year_month from start_date) BETWEEN extract(year_month from start_date) and extract(year_month from ?))", [
                        $request->date,
                        $request->date
                    ])
                )
            ;
        }

        if ($request->type === 'list') $programmations->whereRaw('start_date >= ?', [$request->date]);
        if ($request->type === 'day') $programmations->whereRaw('start_date = ?', [$request->date]);

        return response()->json([
            'message' => __('Programmations listed successfully'),
            'data'    => $programmations->get()
        ], 200);
    }

    public function store(StoreProgrammation $request)
    {
        $data = $request->validated();
        $spaceGroup = [];

        if ($exists = $this->exists($data)) return abort(403, __('Already exists a programmation for this period and space created by') . " {$exists->user->name}");

        $programmation = Programmation::create([
            'user_id'     => auth()->user()->id,
            'schedule_id' => $data['schedule'],
            'category_id' => $data['category'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date']
        ]);

        foreach ($data['spaces'] as $space) $spaceGroup[] = new ProgrammationSpace(['programmation_id' => $programmation->id, 'space_id' => $space]);

        $programmation->spaces()->saveMany($spaceGroup);

        if (isset($data['users']) && count($data['users']) > 0) {
            $userGroup  = [];

            foreach ($data['users'] as $user) $userGroup[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

            $programmation->users()->saveMany($userGroup);
        }

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Criou uma programação chamada " . $data['title']
        ]);

        $this->dispatchNotifications($this->buildNotificationActions($programmation), $programmation);

        return response()->json([
            'message' => __('Programmation created successfully')
        ], 200);
    }

    public function update(UpdateProgrammation $request, $programmation)
    {
        $data = $request->validated();
        $spaceGroup = [];

        if ($exists = $this->exists($data, $programmation)) return abort(403, __('Already exists a programmation for this period and space created by') . " {$exists->user->name}"); 
        
        $actions = $this->buildNotificationActions($programmation, $data);
        $programmation->category_id = $data['category'];
        $programmation->title       = $data['title'];
        $programmation->description = $data['description'];
        $programmation->start_time  = $data['start_time'];
        $programmation->end_time    = $data['end_time'];
        $programmation->start_date  = $data['start_date'];
        $programmation->end_date    = $data['end_date'];

        
        foreach ($data['spaces'] as $space) $spaceGroup[] = new ProgrammationSpace(['programmation_id' => $programmation->id, 'space_id' => $space]);
        
        $programmation->save();
        $programmation->spaces()->delete();
        $programmation->spaces()->saveMany($spaceGroup);

        if (isset($data['users']) && count($data['users']) > 0) {
            $userGroup  = [];

            foreach ($data['users'] as $user) $userGroup[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

            $programmation->users()->delete();
            $programmation->users()->saveMany($userGroup);
        }

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Atualizou a programação " . $programmation->title
        ]);

        $this->dispatchNotifications($actions, $programmation);

        return response()->json([
            'message' => __('Programmation updated successfully')
        ], 200);
    }

    public function updateDates(UpdateProgrammationDate $request, $programmation)
    {
        $data = $request->validated();

        if ($exists = $this->exists($data, $programmation)) return abort(403, __('Already exists a programmation for this period and space created by') . " {$exists->user->name}");

        $actions = $this->buildNotificationActions($programmation, $data);
        $programmation->start_date = $data['start_date'];
        $programmation->end_date   = $data['end_date'];

        $programmation->save();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Mudou o dia da programação " . $programmation->title
        ]);

        $this->dispatchNotifications($actions, $programmation);

        return response()->json([
            'message' => __('Programmation updated successfully')
        ], 200);
    }

    public function destroy(DestroyProgrammation $request, $programmation)
    {
        Log::create([
            'user' => auth()->user()->name,
            'action' => "Removeu a programação " . $programmation->title
        ]);

        $actions = $this->buildNotificationActions($programmation, null, true);
        
        $programmation->delete();

        $this->dispatchNotifications($actions);

        return response()->json([
            'message' => __('Programmation removed successfully')
        ], 200);
    }
}
