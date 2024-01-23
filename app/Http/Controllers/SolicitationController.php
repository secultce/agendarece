<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ProgrammationController;
use App\Models\Log;
use App\Models\Programmation;
use App\Models\ProgrammationSpace;
use App\Models\ProgrammationUser;
use App\Notifications\SolicitationNotification;
use Illuminate\Http\Request;

class SolicitationController extends Controller
{
    public function approve(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('programmation')->with([
                "status" => __('This request link has expired.'),
                "type"   => "danger"
            ]);
        }

        $programmationController = new ProgrammationController;
        $spaces = $request->solicitation->spaces->pluck('id')->all();
        $users  = $request->solicitation->schedule->users->pluck('id')->all();

        if ($exists = $programmationController->exists([
            'schedule'   => $request->solicitation->schedule,
            'spaces'     => $spaces,
            'start_time' => $request->solicitation->start_time,
            'end_time'   => $request->solicitation->end_time,
            'start_date' => $request->solicitation->start_date,
            'end_date'   => $request->solicitation->end_date,
            'loop_days'  => $request->solicitation->loop_days
        ])) {
            return redirect()->route('programmation')->with([
                "status" => __('Already exists a programmation for this period and space created by') . " {$exists->user->name}",
                "type"   => "danger"
            ]);
        }

        $programmation = Programmation::create([
            'user_id'         => auth()->user()->id,
            'occupation_id'   => $request->solicitation->occupation_id,
            'schedule_id'     => $request->solicitation->schedule_id,
            'category_id'     => $request->solicitation->category_id,
            'title'           => $request->solicitation->title,
            'description'     => $request->solicitation->description,
            'parental_rating' => $request->solicitation->parental_rating,
            'start_time'      => $request->solicitation->start_time,
            'end_time'        => $request->solicitation->end_time,
            'start_date'      => $request->solicitation->start_date,
            'end_date'        => $request->solicitation->end_date,
            'loop_days'       => implode(',', $request->solicitation->loop_days),
            'accessibilities' => implode(',', $request->solicitation->accessibilities),
            'requested_at'    => $request->solicitation->created_at,
            'remind_at'       => null,
            'has_reminder'    => false
        ]);

        foreach ($spaces as $space) $spaceGroup[] = new ProgrammationSpace(['programmation_id' => $programmation->id, 'space_id' => $space]);

        $programmation->spaces()->saveMany($spaceGroup);

        if (count($users) > 0) {
            $userGroup  = [];

            foreach ($users as $user) $userGroup[] = new ProgrammationUser(['programmation_id' => $programmation->id, 'user_id' => $user]);

            $programmation->users()->saveMany($userGroup);
        }

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Aprovou uma solicitação de programação chamada " . $request->solicitation->title
        ]);

        $programmationController->dispatchNotifications($programmationController->buildNotificationActions($programmation), $programmation);
        $request->solicitation->user->notify(new SolicitationNotification('approved', $request->solicitation));
        $request->solicitation->delete();

        return redirect()->route('programmation')->with([
            "status" => __('Solicitation approved successfully'),
            "type"   => "success"
        ]);
    }
}
