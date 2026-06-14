<?php

namespace Database\Seeders;

use App\Models\ImcType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StatusTypeSeeder::class,
            StatusSeeder::class,
            IdentificationTypeSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            ImcTypeSeeder::class,
        ]);

        if( config('app.env') === 'local' ) {
            User::factory(500)->create()->each(function ($user) {
                $weigth = fake()->randomFloat(2, 50, 300);
                $imc = $weigth / ($user->height ** 2);
                $imcType = ImcType::where('min_value', '<=', $imc)->where('max_value', '>=', $imc)->first();
                $user->weightControls()->create([
                    'weight' => $weigth,
                    'imc' => $imc,
                    'imc_type_id' => $imcType->id,
                ]);
            });
        }
    }
}
