<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Http\Requests\StoreSector;
use App\Http\Requests\UpdateSector;
use App\Models\Log;

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
            'user_id' => $data['user'],
            'name'    => $data['name'],
            'active'  => $data['active']
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou um setor chamado " . $data['name']
        ]);

        return response()->json([
            'message' => __('Sector created successfully')
        ], 200);
    }

    public function update(UpdateSector $request, $sector)
    {
        $data = $request->validated();

        $sector->user_id = $data['user'];
        $sector->name    = $data['name'];
        $sector->active  = $data['active'];

        $sector->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou o setor " . $sector->name
        ]);

        return response()->json([
            'message' => __('Sector updated successfully')
        ], 200);
    }

    public function toggleActivation($sector)
    {
        $sector->active = !$sector->active;

        $sector->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => ($sector->active ? 'Ativou' : 'Desativou') . " o setor " . $sector->name
        ]);

        return response()->json([
            'message' => __('Sector updated successfully')
        ], 200);
    }

    public function destroy($sector)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu o setor " . $sector->name
        ]);

        $sector->delete();

        return response()->json([
            'message' => __('Sector removed successfully')
        ], 200);
    }
}
