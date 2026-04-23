<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TypeCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'logo' => 'https://picsum.photos/200?random=' . rand(1, 1000),
            'email' => $this->faker->unique()->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'type_company_id' => TypeCompany::inRandomOrder()->first()->id,
        ];
    }
}
