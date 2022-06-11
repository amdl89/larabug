<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(),
            'notes' => $this->faker->text,
            // 'attachableType' => $this->faker->randomElement($this->attachables()),
            // 'attachableId' => fn ($attrs) => $attrs['attachableType']::factory(),
            // 'uploaderId' => User::factory(),
        ];
    }

    private function attachables()
    {
        return [
            Project::class,
            Ticket::class,
        ];
    }
}
