<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;
use App\Http\Requests\StoreSpace;
use App\Http\Requests\UpdateSpace;
use Illuminate\Support\Facades\Hash;

class SpaceController extends Controller
{
    public function list()
    {
        return response()->json([
            'message' => __('Spaces listed successfully'),
            'data'    => Space::all()
        ], 200);
    }

    public function store(StoreSpace $request)
    {
        $data = $request->validated();

        Space::create([
            'name'   => $data['name'],
            'active' => $data['active']
        ]);

        return response()->json([
            'message' => __('Space created successfully')
        ], 200);
    }

    public function update(UpdateSpace $request, $space)
    {
        $data = $request->validated();

        $space->name   = $data['name'];
        $space->active = $data['active'];

        $space->save();

        return response()->json([
            'message' => __('Space updated successfully')
        ], 200);
    }

    public function toggleActivation($space)
    {
        $space->active = !$space->active;

        $space->save();

        return response()->json([
            'message' => __('Space updated successfully')
        ], 200);
    }

    public function destroy($space)
    {
        $space->delete();

        return response()->json([
            'message' => __('Space removed successfully')
        ], 200);
    }
}
