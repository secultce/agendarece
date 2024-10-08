<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgrammationLink;
use App\Http\Requests\StoreProgrammationLink;
use App\Http\Requests\UpdateProgrammationLink;
use App\Models\Log;
use App\Events\NotifyUsers;
use App\Events\ProgrammationLinkChanged;
use App\Http\Requests\DestroyProgrammationLink;

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
        $link = ProgrammationLink::create([
            'programmation_id' => $programmation->id,
            'user_id'          => auth()->user()->id,
            'name'             => $data['name'],
            'link'             => $data['url']
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou um link na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'link_created', $programmation, $link);
        ProgrammationLinkChanged::dispatch($programmation);

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
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou seu próprio link na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'link_updated', $programmation, $link);

        return response()->json([
            'message' => __('Link updated successfully')
        ], 200);
    }

    public function destroy(DestroyProgrammationLink $request, $programmation, $link)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu um link na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'link_destroyed', $programmation, $link->name);
        ProgrammationLinkChanged::dispatch($programmation);

        $link->delete();

        return response()->json([
            'message' => __('Link removed successfully')
        ], 200);
    }
}
