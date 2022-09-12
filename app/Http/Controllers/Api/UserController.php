<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function list()
    {
        return response()->json([
            'message' => __('Users listed successfully'),
            'data'    => User::all()
        ], 200);
    }

    public function store()
    {

    }

    public function update()
    {

    }

    public function toggleActivation()
    {
        
    }

    public function destroy()
    {
        
    }
}
