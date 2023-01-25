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
    public function list()
    {
        $spaces = Space::whereActive(true);

        if (auth()->user()->role->tag !== 'administrator') $spaces->where('sector_id', auth()->user()->sector->id);

        return response()->json([
            'message' => __('Spaces listed successfully'),
            'data'    => $spaces->orderBy('name')->get()
        ], 200);
    }

    public function store(StoreSpace $request)
    {
        $data = $request->validated();

        Space::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? auth()->user()->sector->id : $data['sector'],
            'icon'      => $data['icon']->store('icons', 'public'),
            'name'      => $data['name'],
            'active'    => $data['active']
        ]);

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Criou um espaço chamado " . $data['name']
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

        $space->sector_id = auth()->user()->role->tag !== 'administrator' ? auth()->user()->sector->id : $data['sector'];
        $space->name      = $data['name'];
        $space->active    = $data['active'];

        $space->save();

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Editou o espaço " . $space->name
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
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => ($space->active ? 'Ativou' : 'Desativou') . " o espaço " . $space->name
        ]);

        return response()->json([
            'message' => __('Space updated successfully')
        ], 200);
    }

    public function destroy($space)
    {
        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Removeu o espaço " . $space->name
        ]);

        $space->delete();

        return response()->json([
            'message' => __('Space removed successfully')
        ], 200);
    }
}
