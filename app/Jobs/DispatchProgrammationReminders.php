<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Programmation;
use App\Events\RemindUsers;

class DispatchProgrammationReminders implements ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $programmations = Programmation::where('has_reminder', true)
            ->where('already_reminded', false)
            ->whereRaw('extract(year_month from start_date) = extract(year_month from current_date) and datediff(start_date, current_date) <= remind_at')
            ->get()
        ;

        if ($programmations->isEmpty()) return;

        foreach ($programmations as $programmation) RemindUsers::dispatch($programmation);
    }
}
