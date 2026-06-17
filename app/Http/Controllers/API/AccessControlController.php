<?php

namespace App\Http\Controllers\API;

use App\Actions\AccessControl\RegisterAccess;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccessControlListRequest;
use App\Http\Requests\AccessControlRequest;
use App\Http\Resources\AccessControlResource;
use App\Http\Resources\AccessRegistrationResource;
use App\Models\AccessControl;
use App\Traits\Api\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AccessControlController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Acceso';

    public function index(AccessControlListRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = AccessControl::query()
            ->with(['user', 'status'])
            ->orderByDesc('created_at');

        if (! empty($validated['status_id'])) {
            $query->where('status_id', $validated['status_id']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('identification', 'like', "%{$search}%");
            });
        }

        $meta = [];

        if (isset($validated['per_page']) && ! empty($validated['per_page'])) {
            $perPage = $validated['per_page'] ?? 25;
            $accessControls = $query->paginate($perPage);
            $meta = $this->paginationMeta($accessControls);
        } else {
            $accessControls = $query->get();
        }

        return $this->successResponse(
            AccessControlResource::collection($accessControls),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true),
            $meta
        );
    }

    public function store(AccessControlRequest $request, RegisterAccess $registerAccess): JsonResponse
    {
        try {
            $validated = $request->validated();

            $result = $registerAccess->execute(
                $validated['identification'],
                $validated['date'] ?? null
            );

            return $this->successResponse(
                new AccessRegistrationResource($result),
                MessageHelper::make(self::ENTITY, __FUNCTION__)
            );
        } catch (ModelNotFoundException) {
            return $this->errorResponse(
                'No se encontró un miembro con la identificación proporcionada.',
                ApiStatuses::STATUS_NOT_FOUND
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
