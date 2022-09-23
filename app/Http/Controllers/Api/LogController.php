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
            'data'    => Log::all()
        ], 200);
    }
}
