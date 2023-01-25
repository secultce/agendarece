<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;
use App\Http\Requests\StoreSpace;
use App\Http\Requests\UpdateSpace;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

class SpaceController extends Controller
{
    public function list(Request $request)
    {
        $spaces   = Space::whereActive(true)->orderBy('name');
        $sector   = $request->sector ? $request->sector->id : (auth()->user()->sector ? auth()->user()->sector->id : null);

        if (in_array(auth()->user()->role->tag, ['scheduler', 'responsible', 'user'])) {
            if ($sector) $spaces->where('sector_id', $sector);
            else $spaces->whereNull('sector_id');
        }

        if (auth()->user()->role->tag === 'administrator' && $sector) $spaces->where('sector_id', $sector);

        return response()->json([
            'message' => __('Spaces listed successfully'),
            'data'    => $spaces->get()
        ], 200);
    }

    public function store(StoreSpace $request)
    {
        $data = $request->validated();

        Space::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'],
            'icon'      => $data['icon']->store('icons', 'public'),
            'name'      => $data['name'],
            'active'    => $data['active']
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou um espaço chamado " . $data['name']
        ]);

        return response()->json([
            'message' => __('Space created successfully')
        ], 200);
    }

    public function update(UpdateSpace $request, $space)
    {
        $data = $request->validated();

        if (isset($data['icon'])) {
            Storage::delete($space->icon);

            $space->icon = $data['icon']->store('icons', 'public');
        }

        $space->sector_id = auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'];
        $space->name      = $data['name'];
        $space->active    = $data['active'];

        $space->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou o espaço " . $space->name
        ]);

        return response()->json([
            'message' => __('Space updated successfully')
        ], 200);
    }

    public function toggleActivation($space)
    {
        $space->active = !$space->active;

        $space->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => ($space->active ? 'Ativou' : 'Desativou') . " o espaço " . $space->name
        ]);

        return response()->json([
            'message' => __('Space updated successfully')
        ], 200);
    }

    public function destroy($space)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu o espaço " . $space->name
        ]);

        $space->delete();

        return response()->json([
            'message' => __('Space removed successfully')
        ], 200);
    }
}
