<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Axis;
use App\Http\Requests\StoreAxis;
use App\Http\Requests\UpdateAxis;
use App\Models\Log;

class AxisController extends Controller
{
    public function list(Request $request)
    {
        $axes   = Axis::orderBy('name');
        $sector = $request->sector ? $request->sector->id : (auth()->user()->sector ? auth()->user()->sector->id : null);

        if ($sector) $axes->where('sector_id', $sector);

        return response()->json([
            'message' => __('Axes listed successfully'),
            'data'    => $axes->get()
        ], 200);
    }

    public function store(StoreAxis $request)
    {
        $data = $request->validated();

        Axis::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'],
            'name'      => $data['name'],
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou um eixo chamado " . $data['name']
        ]);

        return response()->json([
            'message' => __('Axis created successfully')
        ], 200);
    }

    public function update(UpdateAxis $request, $axis)
    {
        $data = $request->validated();

        $axis->sector_id = auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'];
        $axis->name      = $data['name'];

        $axis->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou o eixo " . $axis->name
        ]);

        return response()->json([
            'message' => __('Axis updated successfully')
        ], 200);
    }

    public function destroy($axis)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu o eixo " . $axis->name
        ]);

        $axis->delete();

        return response()->json([
            'message' => __('Axis removed successfully')
        ], 200);
    }
}
