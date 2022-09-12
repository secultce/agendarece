<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function list()
    {
        return response()->json([
            'message' => __("Roles listed successfully"),
            'data'    => Role::all()
        ], 200);
    }
}
