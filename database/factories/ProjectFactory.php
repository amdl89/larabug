<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\ProjectPriority;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
            'status' => $this->faker->randomElement($this->statusValues()),
            'deadline' => $this->faker->dateTimeBetween(
                now()->subMonths(2),
                now()->addMonths(8)
            ),
            // 'priorityId' => ProjectPriority::factory(),
        ];
    }

    private function statusValues()
    {
        return collect(ProjectStatus::asArray())->values()->toArray();
    }
}
