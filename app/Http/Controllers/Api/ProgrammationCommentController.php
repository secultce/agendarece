<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgrammationComment;
use App\Http\Requests\StoreProgrammationComment;
use App\Http\Requests\UpdateProgrammationComment;
use App\Http\Requests\DestroyProgrammationComment;
use App\Models\Log;
use App\Events\NotifyUsers;
use App\Events\ProgrammationCommentChanged;

class ProgrammationCommentController extends Controller
{
    public function list($programmation)
    {
        return response()->json([
            'message' => __('Comments listed successfully'),
            'data'    => $programmation->comments
        ], 200);
    }

    public function store(StoreProgrammationComment $request, $programmation)
    {
        $data = $request->validated();
        $comment = ProgrammationComment::create([
            'programmation_id' => $programmation->id,
            'user_id'          => auth()->user()->id,
            'comment'          => $data['text']
        ]);

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Fez um coméntário na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'comment_created', $programmation, $comment->comment);
        ProgrammationCommentChanged::dispatch($programmation);

        return response()->json([
            'message' => __('Comment created successfully')
        ], 200);
    }

    public function update(UpdateProgrammationComment $request, $programmation, $comment)
    {
        $data = $request->validated();

        $comment->comment = $data['text'];

        $comment->save();

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Editou seu próprio coméntário na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'comment_updated', $programmation, $comment->comment);

        return response()->json([
            'message' => __('Comment updated successfully')
        ], 200);
    }

    public function destroy(DestroyProgrammationComment $request, $programmation, $comment)
    {
        $comment->delete();

        Log::create([
            'sector_id' => auth()->user()->sector->id ?? null,
            'user'      => auth()->user()->name,
            'action'    => "Removeu seu próprio coméntário na programação " . $programmation->title
        ]);

        NotifyUsers::dispatch(auth()->user(), 'comment_destroyed', $programmation);
        ProgrammationCommentChanged::dispatch($programmation);

        return response()->json([
            'message' => __('Comment removed successfully')
        ], 200);
    }
}
