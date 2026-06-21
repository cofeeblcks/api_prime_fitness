<?php

namespace App\Http\Controllers\API;

use App\Actions\WeightControls\CreateWeightControl;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\WeightControlRequest;
use App\Http\Resources\SuscriptionResource;
use App\Http\Resources\WeightControlResource;
use App\Models\Suscription;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role.modules', 'qrCode']);

        return $this->successResponse(
            new AuthResource($user),
            'Perfil obtenido correctamente'
        );
    }

    public function subscriptions(Request $request): JsonResponse
    {
        $subscriptions = Suscription::query()
            ->with(['plan', 'type', 'status', 'payments.status'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse(
            SuscriptionResource::collection($subscriptions),
            'Membresías obtenidas correctamente'
        );
    }

    public function weightControls(Request $request): JsonResponse
    {
        $records = $request->user()
            ->weightControls()
            ->with('imcType')
            ->orderByDesc('created_at')
            ->get();

        return $this->successResponse(
            WeightControlResource::collection($records),
            'Historial de peso obtenido correctamente'
        );
    }

    public function storeWeightControl(
        WeightControlRequest $request,
        CreateWeightControl $createWeightControl
    ): JsonResponse {
        try {
            $record = $createWeightControl->execute(
                $request->user(),
                (float) $request->validated('weight')
            );

            return $this->successResponse(
                new WeightControlResource($record),
                'Registro de peso guardado correctamente'
            );
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                ApiStatuses::STATUS_UNPROCESSABLE_ENTITY
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
