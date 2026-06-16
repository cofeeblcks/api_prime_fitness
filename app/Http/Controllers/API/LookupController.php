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
     * Países.
     */
    public function countries(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, Country::class, LookupMessages::COUNTRIES);
    }

    /**
     * Departamentos.
     */
    public function states(StateRequest $request): JsonResponse
    {
        return $this->getResponse($request, State::class, LookupMessages::STATES, function ($query, $validated) {
            if (isset($validated['country_id'])) {
                $query->where('country_id', $validated['country_id']);
            }

            return $query;
        });
    }

    /**
     * Ciudades.
     */
    public function cities(CityRequest $request): JsonResponse
    {
        return $this->getResponse($request, City::class, LookupMessages::CITIES, function ($query, $validated) {
            if (isset($validated['state_id'])) {
                $query->where('state_id', $validated['state_id']);
            }

            return $query;
        });
    }

    /**
     * Tipos de territorios.
     */
    public function territoryTypes(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, TerritoryType::class, LookupMessages::TERRITORY_TYPES);
    }

    /**
     * Localidades
     */
    public function localities(LocalityLookupRequest $request): JsonResponse
    {
        return $this->getResponse($request, Locality::class, LookupMessages::LOCALITIES, function ($query, $validated) {
            if (isset($validated['city_id'])) {
                $query->where('city_id', $validated['city_id']);
            }

            return $query;
        });
    }

    /**
     * Territorios
     */
    public function territories(TerritoryLookupRequest $request): JsonResponse
    {
        return $this->getResponse($request, Territory::class, LookupMessages::TERRITORIES, function ($query, $validated) {
            $query->where('city_id', 686);

            if (isset($validated['territory_type_id'])) {
                $query->where('territory_type_id', $validated['territory_type_id']);
            }

            if (isset($validated['city_id'])) {
                $query->where('city_id', $validated['city_id']);
            }

            if (isset($validated['locality_id'])) {
                $query->where('locality_id', $validated['locality_id']);
            }

            return $query;
        });
    }

    /**
     * Barrios
     */
    public function neighborhoods(NeighborhoodLookupRequest $request): JsonResponse
    {
        return $this->getResponse($request, Neighborhood::class, LookupMessages::NEIGHBORHOODS, function ($query, $validated) {
            if (isset($validated['territory_id'])) {
                $query->where('territory_id', $validated['territory_id']);
            }

            return $query->orderBy('name', 'asc');
        });
    }

    /**
     * Puestos
     */
    public function loci(LocusRequest $request): JsonResponse
    {
        return $this->getResponse($request, Locus::class, LookupMessages::LOCI, function ($query, $validated) {
            if (isset($validated['neighborhood_id'])) {
                $query->where('neighborhood_id', $validated['neighborhood_id']);
            }

            return $query;
        });
    }

    /**
     * Mesas
     */
    public function tables(TableRequest $request): JsonResponse
    {
        return $this->getResponse($request, Table::class, LookupMessages::TABLES, function ($query, $validated) {
            if (isset($validated['locus_id'])) {
                $query->where('locus_id', $validated['locus_id']);
            }

            return $query;
        });
    }

    /**
     * Estados electorales
     */
    public function electoralStatuses(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, ElectoralStatus::class, LookupMessages::ELECTORAL_STATUSES);
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
     * Estados de llamadas
     */
    public function callCenterStatuses(PaginationRequest $request): JsonResponse
    {
        return $this->getResponse($request, Status::class, LookupMessages::CALL_CENTER_STATUSES, function ($query) {
            return $query->where('status_type_id', StatusTypesConstants::CALL_CENTER);
        });
    }
}
