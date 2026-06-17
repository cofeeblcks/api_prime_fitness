<?php


namespace App\Http\Controllers\API;

use App\Constants\LookupMessages;
use App\Constants\StatusTypesConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Http\Requests\LocalityLookupRequest;
use App\Http\Requests\LocusRequest;
use App\Http\Requests\NeighborhoodLookupRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\StateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\TerritoryLookupRequest;
use App\Models\City;
use App\Models\Territory;
use App\Models\Country;
use App\Models\ElectoralStatus;
use App\Models\IdentificationType;
use App\Models\Locality;
use App\Models\Locus;
use App\Models\Neighborhood;
use App\Models\Role;
use App\Models\State;
use App\Models\Status;
use App\Models\Table;
use App\Models\TerritoryType;
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
     * @param PaginationRequest $request Petición validada con posibles parámetros como per_page.
     * @param string $model Clase del modelo Eloquent (por ejemplo: ConceptType::class).
     * @param string $message Descripción legible del recurso, usada en el mensaje de respuesta.
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

        $resourceClass = 'App\\Http\\Resources\\' . class_basename($model) . 'Resource';

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
}
