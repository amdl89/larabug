<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TicketPriority;

class TicketPriorityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketPriority::class;

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
            'Low' => '#82B1FF',
            'Normal' => '#8bc34a',
            'Urgent' => '#ffb107',
            'Severe' => '#cd4631',
        ]);
    }
}
