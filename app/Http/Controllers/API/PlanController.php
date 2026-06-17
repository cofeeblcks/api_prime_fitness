<?php

namespace App\Http\Controllers\API;

use App\Actions\Plans\CreatePlan;
use App\Actions\Plans\UpdatePlan;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanListRequest;
use App\Http\Requests\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Plan';

    public function index(PlanListRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Plan::query()
            ->with('details')
            ->orderBy('name');

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (isset($validated['is_active'])) {
            $query->where('is_active', $validated['is_active']);
        }

        $meta = [];

        if (isset($validated['per_page']) && ! empty($validated['per_page'])) {
            $perPage = $validated['per_page'] ?? 25;
            $plans = $query->paginate($perPage);
            $meta = $this->paginationMeta($plans);
        } else {
            $plans = $query->get();
        }

        return $this->successResponse(
            PlanResource::collection($plans),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true),
            $meta
        );
    }

    public function store(PlanRequest $request): JsonResponse
    {
        try {
            $response = (new CreatePlan)->execute($request->all());

            return $this->successResponse(
                new PlanResource($response['plan']),
                MessageHelper::make(self::ENTITY, __FUNCTION__)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function show(Plan $plan): JsonResponse
    {
        return $this->successResponse(
            new PlanResource($plan->load('details')),
            MessageHelper::make(self::ENTITY, __FUNCTION__)
        );
    }

    public function update(PlanRequest $request, Plan $plan): JsonResponse
    {
        try {
            $response = (new UpdatePlan)->execute($plan->id, $request->all());

            return $this->successResponse(
                new PlanResource($response['plan']),
                MessageHelper::make(self::ENTITY, __FUNCTION__)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $plan->delete();

        return $this->successResponse(
            message: MessageHelper::make(self::ENTITY, __FUNCTION__)
        );
    }
}
