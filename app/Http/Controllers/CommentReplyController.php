<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use JustSteveKing\StatusCode\Http;

class CommentReplyController extends Controller
{
    public function index(Request $request, Comment $comment)
    {
        Gate::authorize('view-all-comment-reply', [$comment]);

        $validator = Validator::make($request->all(), [
            'exclude' => ['sometimes', 'array'],
            'exclude.*' => ['integer'],
            'cursor' => ['sometimes', 'nullable', 'string']
        ]);

        if ($validator->fails())
        {
            return response()->json(
                ['errors' => $validator->errors()->toArray()],
                Http::UNPROCESSABLE_ENTITY
            );
        }

        $replies = $comment->replies()
            ->with([
                'user' => fn ($q1) => $q1->with(
                    ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                )
                    ->select('id')
                    ->withForeignKeys(),
            ])
            ->orderBy('id', 'desc')
            ->when(
                $request->get('exclude'),
                fn ($q, $exclude) => $q->whereNotIn('id', $exclude)
            )
            ->addSelect([
                'can-update' => Comment::from('comments', 'c')
                    ->canBeModifiedBy($request->user())
                    ->whereColumn('comments.id', 'c.id')
                    ->select('id')
                    ->limit(1),
                'can-delete' =>  Comment::from('comments', 'c')
                    ->canBeDeletedBy($request->user())
                    ->whereColumn('comments.id', 'c.id')
                    ->select('id')
                    ->limit(1),
            ])
            ->withCasts([
                'can-update' => 'boolean',
                'can-delete' => 'boolean',
            ])
            ->cursorPaginate();

        return CommentResource::collection($replies)
            ->additional([
                'meta' => [
                    'next_cursor' => optional($replies->nextCursor())->encode(),
                ]
            ]);
    }
}
