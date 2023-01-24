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
            'data'    => auth()->user()->role->tag === 'administrator' ? Role::all() : Role::whereNotIn('tag', ['administrator', 'responsible'])->get()
        ], 200);
    }
}
