<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProjectPriority;

class ProjectPriorityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectPriority::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(
                $this->sampleValues()->keys()->toArray()
            ),
            'color' => fn ($attrs) => $this->sampleValues()->get($attrs['name']),
        ];
    }

    public function sampleValues()
    {
        return collect([
            'Low' => '#362C28',
            'Medium' => '#355070',
            'High' => '#82B1FF',
        ]);
    }
}
