<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programmation;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgrammationController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('programmation');
    }

    public function report(Request $request)
    {
        $programmations = Programmation::where('schedule_id', $request->schedule->id);
        $date           = $request->date;
        $period         = "";

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

        if ($request->spaces) {
            $programmations->whereHas('spaces', function ($query) use ($request) {
                $query->whereIn('space_id', $request->spaces);
            });
        }

        if ($request->categories) $programmations->whereIn('category_id', $request->categories);

        if (is_string($date) || (is_array($date) && count($date) === 1)) {
            $dateSeparation = explode('-', $date);
            $programmations->whereRaw('(year(start_date) >= ? and month(start_date) >= ?) or (? between year(start_date) and year(end_date) and ? between month(start_date) and month(end_date)) or end_date is null', [$dateSeparation[0], $dateSeparation[1], $dateSeparation[0], $dateSeparation[1]]);
            $period = implode('/', array_reverse(explode('-', $date)));
        } else {
            sort($date);

            $programmations->whereRaw('start_date >= ? and end_date <= ?', $date);
            $period = implode('/', array_reverse(explode('-', $date[0]))) . ' a ' . implode('/', array_reverse(explode('-', $date[1])));
        }

        $programmations = $programmations->orderByRaw('start_time, end_time')->get();

        if ($programmations->isEmpty()) {
            return redirect()
                ->route('planning')
                ->with('status', __("Programmations not found"))
            ;
        }

        return Pdf::loadView("report", [
                'programmations' => $programmations,
                'period'         => $period,
                'schedule'       => $request->schedule->name,
            ])
            ->setPaper('a4', 'landscape')
            ->stream("Relatório de Programação {$period}.pdf")
        ;
    }
}
