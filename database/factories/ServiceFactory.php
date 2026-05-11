<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

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
        $imagePath = null;
        $localImages = glob(database_path('seeders/images/services/*.jpg'));
        if (!empty($localImages)) {
            $source = $localImages[array_rand($localImages)];
            $filename = 'services/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, file_get_contents($source));
            $imagePath = $filename;
        }

        return [
            'name'        => $this->faker->randomElement([
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
            'duration'    => $this->faker->numberBetween(15, 120),
            'price'       => $this->faker->numberBetween(10000, 80000),
            'image'       => $imagePath,
            'company_id'  => Company::inRandomOrder()->first()->id,
        ];
    }
}
