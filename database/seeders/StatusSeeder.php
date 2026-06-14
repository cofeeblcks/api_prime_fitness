<?php

namespace Database\Seeders;

use App\Constants\StatusesConstants;
use App\Constants\StatusTypesConstants;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $statuses = [
            // Usuario
            [
                'id' => StatusesConstants::ACTIVE,
                'name' => 'Activo',
                'color' => '#27AE60',
                'status_type_id' => StatusTypesConstants::USER,
            ],
            [
                'id' => StatusesConstants::INACTIVE,
                'name' => 'Inactivo',
                'color' => '#95A5A6',
                'status_type_id' => StatusTypesConstants::USER,
            ],

            // Acceso
            [
                'id' => StatusesConstants::ALLOWED,
                'name' => 'Acceso permitido',
                'color' => '#2980B9',
                'status_type_id' => StatusTypesConstants::ACCESS,
            ],
            [
                'id' => StatusesConstants::DENIED,
                'name' => 'Acceso denegado',
                'color' => '#E74C3C',
                'status_type_id' => StatusTypesConstants::ACCESS,
            ],

            // Membresía
            [
                'id' => StatusesConstants::SUBSCRIPTION_ACTIVE,
                'name' => 'Membresía activa',
                'color' => '#1ABC9C',
                'status_type_id' => StatusTypesConstants::SUBSCRIPTION,
            ],
            [
                'id' => StatusesConstants::SUBSCRIPTION_SUSPENDED,
                'name' => 'Membresía suspendida',
                'color' => '#F39C12',
                'status_type_id' => StatusTypesConstants::SUBSCRIPTION,
            ],
            [
                'id' => StatusesConstants::SUBSCRIPTION_CANCELLED,
                'name' => 'Membresía cancelada',
                'color' => '#C0392B',
                'status_type_id' => StatusTypesConstants::SUBSCRIPTION,
            ],

            // Pago
            [
                'id' => StatusesConstants::PENDING,
                'name' => 'Pago pendiente',
                'color' => '#E67E22',
                'status_type_id' => StatusTypesConstants::PAYMENT,
            ],
            [
                'id' => StatusesConstants::PAID,
                'name' => 'Pago realizado',
                'color' => '#16A085',
                'status_type_id' => StatusTypesConstants::PAYMENT,
            ],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
