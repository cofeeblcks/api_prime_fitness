<?php

namespace App\Actions\Suscriptions;

use App\Models\Suscription;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateSuscription
{
    use FillModelData;

    public function execute(array $data): array
    {
        try {
            DB::beginTransaction();

            $suscription = new Suscription;
            $suscription->fill($this->fillData(Suscription::class, $data));
            $suscription->save();

            DB::commit();

            return [
                'success' => true,
                'suscription' => $suscription,
            ];
        } catch (\Exception $e) {
            Log::channel('SuscriptionError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }
}
