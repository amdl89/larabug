<?php

namespace Database\Seeders;

use App\Enums\ReceivedMessageStatus;
use App\Enums\SentMessageStatus;
use App\Models\Message;
use App\Models\User;
use App\Models\UserReceivedMessage;
use DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Faker\Generator;

class MessageSeeder extends Seeder
{
    protected Generator $faker;

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
        DB::transaction(function ()
        {
            User::all()
                ->each(
                    function (User $user)
                    {
                        Message::factory()
                            ->count($this->faker->numberBetween(5, 10))
                            ->for($user, 'sender')
                            ->make()
                            ->each(fn (Message $message) => $message->saveQuietly());
                    }
                );

            $users = User::all();

            Message::query()
                ->withoutGlobalScope('noDrafts')
                ->with('sender')
                ->get()
                ->each(
                    function (Message $message) use ($users)
                    {
                        $usersCannotReceiveMessageIds = collect([$message->sender->id]);

                        UserReceivedMessage::factory()
                            ->count($this->faker->numberBetween(1, 6))
                            ->for($message, 'message')
                            ->make()
                            ->each(
                                function (UserReceivedMessage $urm) use ($users, $usersCannotReceiveMessageIds, $message)
                                {
                                    if ($message->sentStatus->is(SentMessageStatus::Draft()))
                                    {
                                        $urm->receivedStatus = ReceivedMessageStatus::Unread();
                                    }
                                    $randomUser = $users->filter(
                                        fn (User $user) => $usersCannotReceiveMessageIds->doesntContain($user->id)
                                    )
                                        ->random();

                                    $urm->receipent()
                                        ->associate($randomUser)
                                        ->save();

                                    $usersCannotReceiveMessageIds->push($randomUser->id);
                                }
                            );
                    }
                );
        });
    }
}
