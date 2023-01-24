<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function list()
    {
        $logs = Log::whereRaw('created_at between DATE_ADD(current_timestamp, INTERVAL -1 month) and current_timestamp');

        if (auth()->user()->role->tag === 'responsible') $logs->where('sector_id', auth()->user()->sector->id);

        return response()->json([
            'message' => __('Logs listed successfully'),
            'data'    => $logs->orderByRaw('created_at DESC')->get()
        ], 200);
    }
}
