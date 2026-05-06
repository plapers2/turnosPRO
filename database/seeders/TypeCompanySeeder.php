<?php

namespace Database\Seeders;

use App\Models\TypeCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TypeCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('type-companies');
        Storage::disk('public')->makeDirectory('type-companies');

        $types = [
            'Barbería',
            'Estética',
            'Peluquería',
            'Spa',
            'Salón de Belleza',
            'Veterinaria',
            'Taller Mecánico',
            'Consultorio Médico',
            'Agencia de Viajes'
        ];

        foreach ($types as $type) {
            $logoPath = null;
            $localImages = glob(database_path('seeders/images/type-companies/*.jpg'));
            if (!empty($localImages)) {
                $source = $localImages[array_rand($localImages)];
                $filename = 'type-companies/' . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, file_get_contents($source));
                $logoPath = $filename;
            }

            TypeCompany::create([
                'name' => $type,
                'logo' => $logoPath,
            ]);
        }
    }
}
