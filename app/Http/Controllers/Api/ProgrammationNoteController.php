<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgrammationNote;
use App\Http\Requests\StoreProgrammationNote;
use App\Http\Requests\UpdateProgrammationNote;
use App\Models\Log;

class ProgrammationNoteController extends Controller
{
    public function list($programmation)
    {
        return response()->json([
            'message' => __('Notes listed successfully'),
            'data'    => $programmation->notes
        ], 200);
    }

    public function store(StoreProgrammationNote $request, $programmation)
    {
        $data = $request->validated();

        ProgrammationNote::create([
            'programmation_id' => $programmation->id,
            'user_id'          => auth()->user()->id,
            'note'             => $data['text']
        ]);

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Criou uma nota na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Note created successfully')
        ], 200);
    }

    public function update(UpdateProgrammationNote $request, $programmation, $note)
    {
        $data = $request->validated();

        $note->note = $data['text'];

        $note->save();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Editou sua própria nota na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Note updated successfully')
        ], 200);
    }

    public function destroy($programmation, $note)
    {
        $note->delete();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Removeu sua própria nota na programação " . $programmation->title
        ]);

        return response()->json([
            'message' => __('Note removed successfully')
        ], 200);
    }
}
