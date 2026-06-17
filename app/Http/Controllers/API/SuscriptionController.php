<?php

namespace App\Http\Controllers\API;

use App\Actions\Suscriptions\CreateSuscription;
use App\Actions\Suscriptions\UpdateSuscription;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuscriptionListRequest;
use App\Http\Requests\SuscriptionRequest;
use App\Http\Resources\SuscriptionResource;
use App\Models\Suscription;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class SuscriptionController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Membresía';

    public function index(SuscriptionListRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Suscription::query()
            ->with(['user', 'plan', 'type', 'status'])
            ->orderByDesc('created_at');

        if (! empty($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        if (! empty($validated['plan_id'])) {
            $query->where('plan_id', $validated['plan_id']);
        }

        if (! empty($validated['status_id'])) {
            $query->where('status_id', $validated['status_id']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where('code', 'like', "%{$search}%");
        }

        $meta = [];

        if (isset($validated['per_page']) && ! empty($validated['per_page'])) {
            $perPage = $validated['per_page'] ?? 25;
            $suscriptions = $query->paginate($perPage);
            $meta = $this->paginationMeta($suscriptions);
        } else {
            $suscriptions = $query->get();
        }

        return $this->successResponse(
            SuscriptionResource::collection($suscriptions),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true, false),
            $meta
        );
    }

    public function store(SuscriptionRequest $request): JsonResponse
    {
        try {
            $response = (new CreateSuscription)->execute($request->all());

            return $this->successResponse(
                new SuscriptionResource($response['suscription']->load(['user', 'plan', 'type', 'status'])),
                MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function show(Suscription $suscription): JsonResponse
    {
        return $this->successResponse(
            new SuscriptionResource($suscription->load(['user', 'plan', 'type', 'status', 'payments.status'])),
            MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }

    public function update(SuscriptionRequest $request, Suscription $suscription): JsonResponse
    {
        try {
            $response = (new UpdateSuscription)->execute($suscription->id, $request->all());

            return $this->successResponse(
                new SuscriptionResource($response['suscription']->load(['user', 'plan', 'type', 'status'])),
                MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroy(Suscription $suscription): JsonResponse
    {
        $suscription->delete();

        return $this->successResponse(
            message: MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }
}
