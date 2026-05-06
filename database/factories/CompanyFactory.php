<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TypeCompany;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

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
        $imagePath = null;
        $localImages = glob(database_path('seeders/images/logos/*.jpg'));
        if (!empty($localImages)) {
            $source = $localImages[array_rand($localImages)];
            $filename = 'logos/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, file_get_contents($source));
            $imagePath = $filename;
        }

        return [
            'name'            => $this->faker->company,
            'logo'            => $imagePath,
            'email'           => $this->faker->unique()->companyEmail,
            'phone'           => $this->faker->phoneNumber,
            'address'         => $this->faker->address,
            'type_company_id' => TypeCompany::inRandomOrder()->first()->id,
        ];
    }
}
