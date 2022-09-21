<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgrammationComment;
use App\Http\Requests\StoreProgrammationComment;
use App\Http\Requests\UpdateProgrammationComment;
use App\Http\Requests\DestroyProgrammationComment;

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

        ProgrammationComment::create([
            'programmation_id' => $programmation->id,
            'user_id'          => auth()->user()->id,
            'comment'          => $data['text']
        ]);

        return response()->json([
            'message' => __('Comment created successfully')
        ], 200);
    }

    public function update(UpdateProgrammationComment $request, $programmation, $comment)
    {
        $data = $request->validated();

        $comment->comment = $data['text'];

        $comment->save();

        return response()->json([
            'message' => __('Comment updated successfully')
        ], 200);
    }

    public function destroy(DestroyProgrammationComment $request, $programmation, $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => __('Comment removed successfully')
        ], 200);
    }
}
