<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function list()
    {
        return response()->json([
            'message' => __('Logs listed successfully'),
            'data'    => Log::whereRaw('created_at between DATE_ADD(current_timestamp, INTERVAL -1 month) and current_timestamp')->orderByRaw('created_at DESC')->get()
        ], 200);
    }
}
