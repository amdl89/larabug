<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TicketType;

class TicketTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketType::class;

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
            'UI' => '#faa0a0',
            'Server' => '#607d8b',
            'Maintenance' => '#ffb107',
        ]);
    }
}
