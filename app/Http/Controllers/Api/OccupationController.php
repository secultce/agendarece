<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Occupation;
use App\Http\Requests\StoreOccupation;
use App\Http\Requests\UpdateOccupation;
use App\Models\Log;

class OccupationController extends Controller
{
    public function list(Request $request)
    {
        $occupations = Occupation::orderBy('name');
        $sector      = $request->sector ? $request->sector->id : (auth()->user()->sector ? auth()->user()->sector->id : null);

        if ($sector) $occupations->where('sector_id', $sector);

        return response()->json([
            'message' => __('Occupations listed successfully'),
            'data'    => $occupations->get()
        ], 200);
    }

    public function store(StoreOccupation $request)
    {
        $data = $request->validated();

        Occupation::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'],
            'name'      => $data['name'],
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou uma política de ocupação chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Occupation created successfully')
        ], 200);
    }

    public function update(UpdateOccupation $request, $occupation)
    {
        $data = $request->validated();

        $occupation->sector_id = auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'];
        $occupation->name      = $data['name'];

        $occupation->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou a política de ocupação " . $occupation->name
        ]);

        return response()->json([
            'message' => __('Occupation updated successfully')
        ], 200);
    }

    public function destroy($occupation)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu a política de ocupação " . $occupation->name
        ]);

        $occupation->delete();

        return response()->json([
            'message' => __('Occupation removed successfully')
        ], 200);
    }
}
