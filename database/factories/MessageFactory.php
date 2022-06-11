<?php

namespace Database\Factories;

use App\Enums\SentMessageStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Message;
use App\Models\User;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->text,
            'sentStatus' => $this->faker->randomElement($this->sentStatusValues()),
            // 'senderId' =>  User::factory(),
        ];
    }

    private function sentStatusValues()
    {
        return collect(SentMessageStatus::asArray())->values()->toArray();
    }
}
