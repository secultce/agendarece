<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Http\Requests\StoreSector;
use App\Http\Requests\UpdateSector;

class SectorController extends Controller
{
    public function list(Request $request)
    {
        return response()->json([
            'message' => __('Sectors listed successfully'),
            'data'    => Sector::with('user')->whereActive(true)->get()
        ], 200);
    }

    public function store(StoreSector $request)
    {
        $data = $request->validated();

        Sector::create([
            'user_id' => $data['responsible'],
            'name'    => $data['name'],
            'active'  => $data['active']
        ]);

        return response()->json([
            'message' => __('Sector created successfully')
        ], 200);
    }

    public function update(UpdateSector $request, $sector)
    {
        $data = $request->validated();

        $sector->user_id = $data['responsible'];
        $sector->name    = $data['name'];
        $sector->active  = $data['active'];

        $sector->save();

        return response()->json([
            'message' => __('Sector updated successfully')
        ], 200);
    }

    public function toggleActivation($sector)
    {
        $sector->active = !$sector->active;

        $sector->save();

        return response()->json([
            'message' => __('Sector updated successfully')
        ], 200);
    }

    public function destroy($sector)
    {
        $sector->delete();

        return response()->json([
            'message' => __('Sector removed successfully')
        ], 200);
    }
}
