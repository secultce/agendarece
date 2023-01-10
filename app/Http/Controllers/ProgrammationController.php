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
            $period = ucfirst(\Carbon\Carbon::parse($date)->formatLocalized('%B de %Y'));
        } else {
            sort($date);

            $programmations->whereRaw('start_date >= ? and end_date <= ?', $date);
            $period = ucfirst(\Carbon\Carbon::parse($date[0])->formatLocalized('%B de %Y')) . ' a ' . ucfirst(\Carbon\Carbon::parse($date[1])->formatLocalized('%B de %Y'));
        }

        $programmations = $programmations->orderByRaw('start_date, end_date')->get();

        if ($programmations->isEmpty()) {
            return redirect()
                ->route('planning')
                ->with('status', __("Programmations not found"))
            ;
        }

        return Pdf::loadView("report", [
                'logo'           => base64_encode(Configuration::first()->logo_content),
                'programmations' => $programmations,
                'period'         => $period,
                'schedule'       => $request->schedule->name,
            ])
            ->setOption(['defaultFont' => 'arial'])
            ->setPaper('a4', 'portrait')
            ->stream("Programação Corrida {$period}.pdf")
        ;
    }
}
