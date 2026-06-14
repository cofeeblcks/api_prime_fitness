<?php

namespace Database\Seeders;

use App\Constants\StatusTypesConstants;
use App\Models\StatusType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        StatusType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        StatusType::create([
            'id' => StatusTypesConstants::USER,
            'name' => 'Usuarios',
        ]);

        StatusType::create([
            'id' => StatusTypesConstants::ACCESS,
            'name' => 'Accesos',
        ]);

        StatusType::create([
            'id' => StatusTypesConstants::SUBSCRIPTION,
            'name' => 'Suscripociones',
        ]);

        StatusType::create([
            'id' => StatusTypesConstants::PAYMENT,
            'name' => 'Pagos',
        ]);
    }
}
