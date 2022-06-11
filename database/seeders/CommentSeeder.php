<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ticket;
use DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Faker\Generator;

class CommentSeeder extends Seeder
{
    protected Generator $faker;
    protected int $maxLevel = 3;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(
            function ()
            {
                $this->seedTopLevelComments();

                Comment::whereDoesntHave('parent')
                    ->with('ticket.project.users')
                    ->get()
                    ->each(fn (Comment $comment) => $this->seedReplies($comment));
            }
        );
    }

    private function seedTopLevelComments()
    {
        Ticket::query()
            ->with('project.users')
            ->get()
            ->each(
                function (Ticket $ticket)
                {
                    Comment::factory()
                        ->times($this->faker->numberBetween(3, 6))
                        ->for($ticket, 'ticket')
                        ->make()
                        ->each(function (Comment $comment) use ($ticket)
                        {
                            $comment->user()->associate($ticket->project->users->random())->save();
                        });
                }
            );
    }

    private function seedReplies(Comment $comment)
    {
        $_seedReplies = function (Comment $rootComment, $usersThatCanReply, $level = 1) use (&$_seedReplies)
        {
            if ($level > $this->maxLevel)
                return;

            Comment::factory()
                ->times($this->faker->numberBetween(0, 3))
                ->for($rootComment->ticket, 'ticket')
                ->for($rootComment, 'parent')
                ->make()
                ->transform(
                    function (Comment $comment) use ($usersThatCanReply)
                    {
                        $comment->user()->associate($usersThatCanReply->random())->save();
                        return $comment;
                    }
                )
                ->each(fn (Comment $comment) => $_seedReplies($comment, $usersThatCanReply, $level + 1));
        };

        $_seedReplies($comment, $comment->ticket->project->users);
    }
}
