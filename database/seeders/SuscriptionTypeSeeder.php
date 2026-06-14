<?php

namespace Database\Seeders;

use App\Models\SuscriptionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuscriptionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SuscriptionType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $suscriptionTypes = [
            [
                'id' => 1,
                'name' => 'Diario',
            ],
            [
                'id' => 2,
                'name' => 'Semanal',
            ],
            [
                'id' => 3,
                'name' => 'Mensual',
            ],
            [
                'id' => 4,
                'name' => 'Trimestral',
            ],
            [
                'id' => 5,
                'name' => 'Semestral',
            ],
            [
                'id' => 6,
                'name' => 'Anual',
            ],
            [
                'id' => 7,
                'name' => 'Vitalicio',
            ],
        ];

        foreach ($suscriptionTypes as $suscriptionType) {
            SuscriptionType::create($suscriptionType);
        }
    }
}
