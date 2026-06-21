<?php

namespace App\Http\Controllers\API;

use App\Constants\LookupMessages;
use App\Constants\StatusTypesConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
use App\Models\IdentificationType;
use App\Models\LinkType;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Status;
use App\Models\SuscriptionType;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    use ApiResponse;

    /**
     * Genera una respuesta estándar paginada para recursos tipo "lookup".
     *
     * Este método permite reutilizar la lógica de paginación y respuesta formateada
     * para distintos modelos de lookups
     *
     * @param  PaginationRequest  $request  Petición validada con posibles parámetros como per_page.
     * @param  string  $model  Clase del modelo Eloquent (por ejemplo: ConceptType::class).
     * @param  string  $message  Descripción legible del recurso, usada en el mensaje de respuesta.
     * @return JsonResponse Respuesta JSON con los datos paginados y metadatos adicionales.
     */
    public function getResponse(PaginationRequest $request, string $model, string $message, ?callable $filter = null): JsonResponse
    {
        $validated = $request->validated();

        $query = $model::query();

        if ($filter) {
            $query = $filter($query, $validated);
        }

        $perPage = $validated['per_page'] ?? 25;
        $data = $query->paginate($perPage);

        $resourceClass = 'App\\Http\\Resources\\'.class_basename($model).'Resource';

        return $this->successResponse(
            $resourceClass::collection($data),
            $message,
            $this->paginationMeta($data)
        );
    }

    /**
     * Tipos de identificación.
     */
    public function identificationTypes(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, IdentificationType::class, LookupMessages::IDENTIFICATION_TYPES);
    }

    /**
     * Roles.
     */
    public function roles(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, Role::class, LookupMessages::ROLES, function ($query) {
            return $query->with('modules');
        });
    }

    /**
     * Planes activos.
     */
    public function plans(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, Plan::class, LookupMessages::PLANS, function ($query) {
            return $query
                ->where('is_active', true)
                ->with('details')
                ->orderBy('name');
        });
    }

    /**
     * Tipos de membresía.
     */
    public function suscriptionTypes(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, SuscriptionType::class, LookupMessages::SUSCRIPTION_TYPES, function ($query) {
            return $query->orderBy('name');
        });
    }

    /**
     * Tipos de link
     */
    public function linkTypes(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, LinkType::class, LookupMessages::LINK_TYPES, function ($query) {
            return $query->orderBy('name');
        });
    }

    /**
     * Estados de contacto (Solicitud, Contestado).
     */
    public function contactStatuses(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, Status::class, LookupMessages::CONTACT_STATUSES, function ($query) {
            return $query
                ->where('status_type_id', StatusTypesConstants::CONTACT)
                ->orderBy('id');
        });
    }
}
