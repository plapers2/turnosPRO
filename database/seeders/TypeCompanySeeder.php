<?php

namespace Database\Seeders;

use App\Models\TypeCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            TypeCompany::create([
                'name' => $type
            ]);
        }
    }
}
