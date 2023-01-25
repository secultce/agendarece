<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomHoliday;
use App\Http\Requests\StoreCustomHoliday;
use App\Http\Requests\UpdateCustomHoliday;
use App\Models\Log;

class CustomHolidayController extends Controller
{
    public function list(Request $request)
    {
        return response()->json([
            'message' => __('Custom holidays listed successfully'),
            'data'    => auth()->user()->role->tag === 'administrator' ? CustomHoliday::get() : CustomHoliday::where('sector_id', auth()->user()->sector->id)->get()
        ], 200);
    }

    public function store(StoreCustomHoliday $request)
    {
        $data = $request->validated();

        CustomHoliday::create([
            'sector_id' => auth()->user()->role->tag === 'responsible' ? auth()->user()->sector->id : $data['sector'],
            'name'      => $data['name'],
            'start_at'  => $data['start_at'],
            'end_at'    => $data['end_at']
        ]);

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Criou uma data comemorativa chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Custom holiday created successfully')
        ], 200);
    }

    public function update(UpdateCustomHoliday $request, $customHoliday)
    {
        $data = $request->validated();

        $customHoliday->sector_id = auth()->user()->role->tag === 'responsible' ? auth()->user()->sector->id : $data['sector'];
        $customHoliday->name      = $data['name'];
        $customHoliday->start_at  = $data['start_at'];
        $customHoliday->end_at    = $data['end_at'];

        $customHoliday->save();

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Editou a data comemorativa " . $customHoliday->name
        ]);

        return response()->json([
            'message' => __('Custom holiday updated successfully')
        ], 200);
    }

    public function destroy($customHoliday)
    {
        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Removeu a data comemorativa " . $customHoliday->name
        ]);

        $customHoliday->delete();

        return response()->json([
            'message' => __('Custom holiday removed successfully')
        ], 200);
    }
}
