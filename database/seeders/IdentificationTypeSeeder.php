<?php

namespace Database\Seeders;

use App\Models\IdentificationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdentificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        IdentificationType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $identificationTypes = [
            [
                'id' => 1,
                'name' => 'Cédula de ciudadanía',
                'abbreviation' => 'CC',
            ],
            [
                'id' => 2,
                'name' => 'Cédula de extranjería',
                'abbreviation' => 'CE',
            ],
            [
                'id' => 3,
                'name' => 'Tarjeta de identidad',
                'abbreviation' => 'TI',
            ],
        ];

        foreach ($identificationTypes as $identificationType) {
            IdentificationType::create($identificationType);
        }
    }
}
