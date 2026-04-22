<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Corte de cabello',
                'Manicure',
                'Pedicure',
                'Masaje relajante',
                'Limpieza facial',
                'Barba',
                'Tinte de cabello',
                'Depilación',
            ]),
            'description' => $this->faker->sentence(10),
            'duration' => $this->faker->numberBetween(15, 120), // minutos
            'price' => $this->faker->numberBetween(10000, 80000),
            'image' => 'https://picsum.photos/300?random=' . rand(),
            'company_id' => Company::inRandomOrder()->first()->id,
        ];
    }
}
