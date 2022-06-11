<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text,
            'status' => $this->faker->randomElement($this->statusValues()),
            // 'priorityId' => TicketPriority::factory(),
            // 'typeId' => TicketType::factory(),
            // 'creatorId' => User::factory(),
            // 'assigneeId' => User::factory(),
            // 'projectId' => Project::factory(),
        ];
    }

    private function statusValues()
    {
        return collect(TicketStatus::asArray())->values()->toArray();
    }
}
