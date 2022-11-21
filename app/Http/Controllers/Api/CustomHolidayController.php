<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomHoliday;
use App\Http\Requests\StoreCustomHoliday;
use App\Http\Requests\UpdateCustomHoliday;

class CustomHolidayController extends Controller
{
    public function list(Request $request)
    {
        return response()->json([
            'message' => __('Custom holidays listed successfully'),
            'data'    => CustomHoliday::all()
        ], 200);
    }

    public function store(StoreCustomHoliday $request)
    {
        $data = $request->validated();

        CustomHoliday::create([
            'name'     => $data['name'],
            'start_at' => $data['start_at'],
            'end_at'   => $data['end_at']
        ]);

        return response()->json([
            'message' => __('Custom holiday created successfully')
        ], 200);
    }

    public function update(UpdateCustomHoliday $request, $customHoliday)
    {
        $data = $request->validated();

        $customHoliday->name     = $data['name'];
        $customHoliday->start_at = $data['start_at'];
        $customHoliday->end_at   = $data['end_at'];

        $customHoliday->save();

        return response()->json([
            'message' => __('Custom holiday updated successfully')
        ], 200);
    }

    public function destroy($customHoliday)
    {
        $customHoliday->delete();

        return response()->json([
            'message' => __('Custom holiday removed successfully')
        ], 200);
    }
}
