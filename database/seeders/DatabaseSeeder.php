<?php

namespace Database\Seeders;

use App\Actions\QrCodes\CreateQrCode;
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
            SuscriptionTypeSeeder::class,
            ImcTypeSeeder::class,
            LinkTypeSeeder::class,
            CompanySeeder::class,
        ]);

        if (config('app.env') === 'local') {
            User::factory(500)->create()->each(function ($user) {
                $weight = fake()->randomFloat(2, 50, 300);
                $imc = $weight / ($user->height ** 2);
                $imcType = ImcType::query()
                    ->where('min_value', '<=', $imc)
                    ->where('max_value', '>=', $imc)
                    ->orderByDesc('min_value')
                    ->first();

                $user->weightControls()->create([
                    'weight' => $weight,
                    'imc' => $imc,
                    'imc_type_id' => $imcType->id,
                ]);

                if( $user->qrCodes->isEmpty() ){
                    (new CreateQrCode($user))->execute();
                }
            });
        }
    }
}
