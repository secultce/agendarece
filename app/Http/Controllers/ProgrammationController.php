<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programmation;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Configuration;

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
        $configuration  = Configuration::where('sector_id', auth()->user()->sector->id ?? null)->first();
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
                if ($request->input('negative-spaces') === '1') {
                    $query->whereNotIn('space_id', $request->spaces);

                    return;
                }

                $query->whereIn('space_id', $request->spaces);
            });
        }

        if ($request->categories) {
            if ($request->input('negative-categories') === '1') {
                $programmations->whereNotIn('category_id', $request->categories);
            } else {
                $programmations->whereIn('category_id', $request->categories);
            }
        }

        if (is_string($date) || (is_array($date) && count($date) === 1)) {
            $dateSeparation = explode('-', $date);
            $programmations->whereRaw('((year(start_date) >= ? and month(start_date) >= ?) or (? between year(start_date) and year(end_date) and ? between month(start_date) and month(end_date)) or end_date is null)', [$dateSeparation[0], $dateSeparation[1], $dateSeparation[0], $dateSeparation[1]]);
            $period = ucfirst(\Carbon\Carbon::parse($date)->formatLocalized('%B de %Y'));
        } else {
            sort($date);

            $startDateSeparation = explode('-', $date[0]);
            $endDateSeparation = explode('-', $date[1]);
            $programmations->whereRaw('((year(start_date) >= ? and month(start_date) >= ?) and (year(start_date) <= ? and month(start_date) <= ?))', [$startDateSeparation[0], $startDateSeparation[1], $endDateSeparation[0], $endDateSeparation[1]]);
            $period = ucfirst(\Carbon\Carbon::parse($date[0])->formatLocalized('%B de %Y')) . ' a ' . ucfirst(\Carbon\Carbon::parse($date[1])->formatLocalized('%B de %Y'));
        }

        $programmations = $programmations->orderByRaw('start_date, end_date')->get();

        if ($programmations->isEmpty()) {
            return redirect()
                ->route('programmation')
                ->with('status', __("Programmations not found"))
            ;
        }

        return Pdf::loadView("report", [
                'logo'           => $configuration ? base64_encode($configuration->logo_content) : null,
                'programmations' => $programmations,
                'period'         => $period,
                'schedule'       => $request->schedule->name,
            ])
            ->setOption(['defaultFont' => 'arial'])
            ->setPaper('a4', 'portrait')
            ->stream("Programação {$period}.pdf")
        ;
    }
}
