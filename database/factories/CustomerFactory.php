<?php

namespace Database\Factories;

use Faker\Generator;
use Faker\Provider\pt_BR\Person as FakerPersonBrProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Generator();
        $fakerBr = new FakerPersonBrProvider($faker);
        $faker->addProvider($fakerBr);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'document_number' => $faker->cpf(false)
        ];
    }
}
