<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgrammationLink;
use App\Http\Requests\StoreProgrammationLink;
use App\Http\Requests\UpdateProgrammationLink;
use App\Models\Log;

class ProgrammationLinkController extends Controller
{
    public function list($programmation)
    {
        return response()->json([
            'message' => __('Links listed successfully'),
            'data'    => $programmation->links
        ], 200);
    }

    public function store(StoreProgrammationLink $request, $programmation)
    {
        $data = $request->validated();

        ProgrammationLink::create([
            'programmation_id' => $programmation->id,
            'user_id'          => auth()->user()->id,
            'name'             => $data['name'],
            'link'             => $data['url']
        ]);

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Criou um link na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Link created successfully')
        ], 200);
    }

    public function update(UpdateProgrammationLink $request, $programmation, $link)
    {
        $data = $request->validated();

        $link->name = $data['name'];
        $link->link = $data['url'];

        $link->save();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Editou seu próprio link na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Link updated successfully')
        ], 200);
    }

    public function destroy($programmation, $link)
    {
        $link->delete();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Removeu seu próprio link na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Link removed successfully')
        ], 200);
    }
}
