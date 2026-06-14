<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            [
                'id' => RoleEnum::ADMIN->value,
                'name' => RoleEnum::ADMIN->label(),
            ],
            [
                'id' => RoleEnum::RECEPTIONIST->value,
                'name' => RoleEnum::RECEPTIONIST->label(),
            ],
            [
                'id' => RoleEnum::TRAINING->value,
                'name' => RoleEnum::TRAINING->label(),
            ],
            [
                'id' => RoleEnum::MEMBER->value,
                'name' => RoleEnum::MEMBER->label(),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
