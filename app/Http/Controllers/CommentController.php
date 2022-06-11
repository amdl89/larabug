<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use JustSteveKing\StatusCode\Http;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        Gate::authorize('store-comment', [Ticket::findOrFail($request->get('ticket'))]);

        $validator = Validator::make($request->all(), [
            'body' => ['required', 'between:5,500'],
            'parent' => ['nullable', 'exists:comments,id'],
            'ticket' => ['required', 'exists:tickets,id'],
        ]);

        if ($validator->fails())
        {
            return response()->json(
                ['errors' => $validator->errors()->toArray()],
                Http::UNPROCESSABLE_ENTITY
            );
        }

        $comment = Comment::make([
            'body' => $request->get('body'),
        ]);

        $comment->parent()->associate($request->get('parent'));
        $comment->ticket()->associate($request->get('ticket'));
        $comment->user()->associate($request->user());

        $comment->save();

        return CommentResource::make($comment->load(['user.profile.media']))
            ->response()
            ->setStatusCode(Http::CREATED);
    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update-comment', [$comment]);

        $validator = Validator::make($request->all(), [
            'body' => ['required', 'between:5,500'],
        ]);

        if ($validator->fails())
        {
            return response()->json(
                ['errors' => $validator->errors()->toArray()],
                Http::UNPROCESSABLE_ENTITY
            );
        }

        $comment->update([
            'body' => $request->get('body'),
        ]);

        return response()->noContent();
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('destroy-comment', [$comment]);

        $comment->delete();

        return response()->noContent();
    }
}
