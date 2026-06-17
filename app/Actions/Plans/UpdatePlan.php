<?php

namespace App\Actions\Plans;

use App\Models\Plan;
use App\Models\PlanDetail;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdatePlan
{
    use FillModelData;

    public function execute(int $planId, array $data): array
    {
        try {
            DB::beginTransaction();

            $plan = Plan::findOrFail($planId);
            $plan->fill($this->fillData(Plan::class, $data));
            $plan->save();

            if (array_key_exists('details', $data)) {
                $this->syncDetails($plan, $data['details'] ?? []);
            }

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
        $keptIds = [];

        foreach ($details as $detail) {
            if (! empty($detail['id'])) {
                $planDetail = PlanDetail::query()
                    ->where('plan_id', $plan->id)
                    ->findOrFail($detail['id']);

                $planDetail->update([
                    'item' => $detail['item'],
                    'is_active' => $detail['isActive'] ?? true,
                ]);

                $keptIds[] = $planDetail->id;
            } else {
                $created = $plan->details()->create([
                    'item' => $detail['item'],
                    'is_active' => $detail['isActive'] ?? true,
                ]);

                $keptIds[] = $created->id;
            }
        }

        $plan->details()->whereNotIn('id', $keptIds)->delete();
    }
}
