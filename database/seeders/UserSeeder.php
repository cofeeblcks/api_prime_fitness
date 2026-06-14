<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\SexEnum;
use App\Models\IdentificationType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'first_name' => 'Gestor',
            'last_name' => 'Plataforma',
            'email' => 'gestor.plataforma@primefitness.com',
            'phone' => '1234567890',
            'birthdate' => '1988-01-15',
            'sex' => SexEnum::MALE->value,
            'identification' => '123456789',
            'photo' => null,
            'height' => 1.80,
            'password' => Hash::make('passw0rd'),
            'role_id' => RoleEnum::ADMIN->value,
            'identification_type_id' => IdentificationType::where('abbreviation', 'CC')->first()->id,
        ]);
    }
}
