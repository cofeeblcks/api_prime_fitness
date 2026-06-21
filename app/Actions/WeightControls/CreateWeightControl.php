<?php

namespace App\Actions\WeightControls;

use App\Models\ImcType;
use App\Models\User;
use App\Models\WeightControl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateWeightControl
{
    public function execute(User $user, float $weight): WeightControl
    {
        try {
            DB::beginTransaction();

            if (! $user->height || $user->height <= 0) {
                throw new \InvalidArgumentException('Debes registrar tu estatura en el perfil antes de controlar tu peso.');
            }

            $heightMeters = $user->height / 100;
            $imc = $weight / ($heightMeters ** 2);

            $imcType = ImcType::query()
                ->where('min_value', '<=', $imc)
                ->where('max_value', '>=', $imc)
                ->orderByDesc('min_value')
                ->first();

            if (! $imcType) {
                throw new \InvalidArgumentException('No se pudo determinar la categoría de IMC.');
            }

            $record = $user->weightControls()->create([
                'weight' => $weight,
                'imc' => $imc,
                'imc_type_id' => $imcType->id,
            ]);

            DB::commit();

            return $record->load('imcType');
        } catch (\Exception $e) {
            Log::channel('WeightControlError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }
}
