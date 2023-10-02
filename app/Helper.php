<?php

namespace App;

use Carbon\Carbon;

class Helper
{
    public static function formattedDate($startDate, $endDate, $format = "%B %d")
    {
        $auxStartDate = ucfirst(Carbon::parse($startDate)->formatLocalized($format));
        $auxEndDate   = $endDate ? ucfirst(Carbon::parse($endDate)->formatLocalized($format)) : 'Indefinido';

        return "{$auxStartDate} a {$auxEndDate}";
    }

    public static function formattedTime($startTime, $endTime)
    {
        $startTime = Carbon::parse($startTime)->format('H:i');
        $endTime   = Carbon::parse($endTime)->format('H:i');

        return "{$startTime} as {$endTime}";
    }

    public static function formattedPeriod($programmation, $format = "%d %B")
    {
        $startDate = ucfirst(Carbon::parse($programmation->start_date)->formatLocalized($format));
        $startTime = Carbon::parse($programmation->start_time)->format('H:i');
        $endDate   = $programmation->end_date ? ucfirst(Carbon::parse($programmation->end_date)->formatLocalized($format)) : 'Indefinido';
        $endTime   = Carbon::parse($programmation->end_time)->format('H:i');

        return "{$startDate} até {$endDate} das {$startTime} as {$endTime}";
    }
}