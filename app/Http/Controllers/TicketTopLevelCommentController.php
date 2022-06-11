<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketTopLevelCommentController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        Gate::authorize('view-all-ticket-top-level-comment', [$ticket]);

        $topLevelComments = $ticket->topLevelComments()
            ->with([
                'user' => fn ($q1) => $q1->with(
                    ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                )
                    ->select('id')
                    ->withForeignKeys(),
            ])
            ->addSelect([
                'can-update' => Comment::from('comments', 'c')
                    ->canBeModifiedBy($request->user())
                    ->whereColumn('comments.id', 'c.id')
                    ->select('id')
                    ->limit(1),
                'can-delete' => Comment::from('comments', 'c')
                    ->canBeDeletedBy($request->user())
                    ->whereColumn('comments.id', 'c.id')
                    ->select('id')
                    ->limit(1),
            ])
            ->withCasts([
                'can-update' => 'boolean',
                'can-delete' => 'boolean',
            ])
            ->orderBy('id', 'desc')
            ->cursorPaginate();

        return  CommentResource::collection($topLevelComments)
            ->additional([
                'meta' => [
                    'next_cursor' => optional($topLevelComments->nextCursor())->encode(),
                ]
            ]);
    }
}
