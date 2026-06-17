<?php

namespace App\Actions\Plans;

use App\Models\Plan;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreatePlan
{
    use FillModelData;

    public function execute(array $data): array
    {
        try {
            DB::beginTransaction();

            $plan = new Plan;
            $plan->fill($this->fillData(Plan::class, $data));
            $plan->save();

            $this->syncDetails($plan, $data['details'] ?? []);

            DB::commit();

            return [
                'success' => true,
                'plan' => $plan->load('details'),
            ];
        } catch (\Exception $e) {
            Log::channel('PlanError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $details
     */
    private function syncDetails(Plan $plan, array $details): void
    {
        foreach ($details as $detail) {
            $plan->details()->create([
                'item' => $detail['item'],
                'is_active' => $detail['isActive'] ?? true,
            ]);
        }
    }
}
