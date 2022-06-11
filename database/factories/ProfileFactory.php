<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'title' => $this->faker->jobTitle,
            'bio' => $this->faker->paragraph(5),
            'education' => $this->faker->sentence(),
            'address' => $this->faker->streetAddress,
            // 'userId' => User::factory(),
        ];
    }
}
