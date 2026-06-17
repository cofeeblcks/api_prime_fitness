<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Module::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $modules = [
            [
                'id' => 1,
                'name' => 'Métricas',
                'route' => 'metrics',
                'icon' => 'chart-bar-line',
                'order' => 1,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 2,
                'name' => 'Miembros',
                'route' => 'members',
                'icon' => 'user-group-03',
                'order' => 2,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 3,
                'name' => 'Entrenadores',
                'route' => 'trainers',
                'icon' => 'workout-gymnastics',
                'order' => 3,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 4,
                'name' => 'Planes',
                'route' => 'plans',
                'icon' => 'list-tree',
                'order' => 4,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 5,
                'name' => 'Membresías',
                'route' => 'payments',
                'icon' => 'payment-01',
                'order' => 5,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 6,
                'name' => 'Usuarios',
                'route' => 'users',
                'icon' => 'user-settings-01',
                'order' => 6,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ],
            [
                'id' => 7,
                'name' => 'Control de acceso',
                'route' => 'access-control',
                'icon' => 'device-access',
                'order' => 7,
                'is_active' => true,
                'roles' => [
                    RoleEnum::ADMIN->value,
                ],
            ]
        ];

        foreach ($modules as $module) {
            $roles = $module['roles'];
            unset($module['roles']);

            $module = Module::create($module);
            $module->roles()->detach();
            $module->roles()->attach($roles);
        }
    }
}
