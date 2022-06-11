<?php

namespace Database\Factories;

use App\Enums\ReceivedMessageStatus;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\UserReceivedMessage;

class UserReceivedMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserReceivedMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'receivedStatus' => $this->faker->randomElement($this->receivedStatusValues()),
            // 'receiverId' => User::factory(),
            // 'messageId' => Message::factory(),
        ];
    }


    public function receivedStatusValues()
    {
        return collect(ReceivedMessageStatus::asArray())->values()->toArray();
    }
}
