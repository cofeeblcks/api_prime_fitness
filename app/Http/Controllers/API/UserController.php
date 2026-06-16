<?php

namespace App\Http\Controllers\API;

use App\Actions\Users\CreateUser;
use App\Actions\Users\UpdateUser;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Enums\RoleEnum;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApproveUsersRequest;
use App\Http\Requests\UserChildrenRequest;
use App\Http\Requests\UserIdRequest;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserBasicDataResource;
use App\Http\Resources\UserParentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\NationalRegistryService;
use App\Services\User\ApproveUsersService;
use App\Traits\Actions\UserRelationsTrait;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	use ApiResponse, UserRelationsTrait;

	private const ENTITY = 'Usuario';

	/**
	 * Display a listing of the resource.
	 */
	public function index(UserIndexRequest $request): JsonResponse
	{
		$validated = $request->validated();

		// Determinar si se deben obtener todos los datos o un subconjunto
		$allData = $validated['full_data'] ?? true;

		/** @var \App\Models\User $user */
		$user = Auth::user();

		// Verificar si el usuario tiene permiso para ver todos los usuarios
		$canViewAll = in_array($user->role_id, [
			RoleEnum::ADMIN->value,
			RoleEnum::CALL_CENTER->value,
		]);

		// Determinar si se deben mostrar todos los usuarios o solo los visibles para el usuario
		$all = ($validated['all'] ?? false) && $canViewAll;

		// Definir las relaciones a cargar según el parámetro full_data
		$relations = $allData
			? $this->userRelations(['user.user.user.role', 'callCenters.surveyResponses.electionType'])
			: ['role', 'callCenters', 'users'];

		// Construir la consulta
		$query = User::query()
			->with($relations)
			->orderBy('first_name')
			->orderBy('last_name');

		// Filtro por rol
		if (isset($validated['roles']) && is_array($validated['roles'])) {
			$query->whereIn('role_id', $validated['roles']);
		}

		// Búsqueda
		if (!empty($validated['search'])) {
			$terms = preg_split('/\s+/', trim($validated['search']));

			// Obtener IDs recursivos
			$recursiveIds = $user->getRecursiveUserIds();

			$query->whereIn('id', $recursiveIds)
				->where(function ($q) use ($terms) {
					$q->where(function ($query) use ($terms) {
						foreach ($terms as $term) {
							$query->where(function ($subQuery) use ($term) {
								$subQuery->where('first_name', 'like', "%{$term}%")
										->orWhere('last_name', 'like', "%{$term}%")
										->orWhere('identification', 'like', "%{$term}%");
							});
						}
					});
				});
		} else {
			// Aplicar visibilidad según el rol del usuario autenticado
			$query->visibleFor($user, $all);
		}

		$meta = [];

		// Paginación
		if (isset($validated['per_page']) && !empty($validated['per_page'])) {
			// Aplicar paginación
			$perPage = $validated['per_page'] ?? 25;
			$users = $query->paginate($perPage);
			$meta = $this->paginationMeta($users);
		}
		else {
			// Obtener todos los resultados sin paginación
			$users = $query->get();
		}

		// Preparar la colección de recursos según el parámetro full_data
		$resource = $allData
			? UserResource::collection($users)
			: UserParentResource::collection($users);

		return $this->successResponse(
			$resource,
			MessageHelper::make(self::ENTITY, __FUNCTION__, true),
			$meta
		);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(UserRequest $request): JsonResponse
	{
		try {
			$response = (new CreateUser())->execute($request->all());

			$user = $response['user'];

			return $this->successResponse(
				new UserResource($user->load($this->userRelations())),
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

	/**
	 * Display the specified resource.
	 */
	public function show(User $user): JsonResponse
	{
		return $this->successResponse(
			new UserResource($user->load($this->userRelations())),
			MessageHelper::make(self::ENTITY, __FUNCTION__)
		);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UserRequest $request, User $user): JsonResponse
	{
		try {
			$response = (new UpdateUser())->execute($user->id, $request->all());

			$user = $response['user'];

			return $this->successResponse(
				new UserResource($user->load($this->userRelations())),
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

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(User $user): JsonResponse
	{
		$user->delete();

		return $this->successResponse(
			message: MessageHelper::make(self::ENTITY, __FUNCTION__)
		);
	}

	/**
	 * Get user data by identification
	 */
	public function getByIdentification(string $identification): JsonResponse
	{
		try {
			$result = (new NationalRegistryService())->getUserByIdentification($identification);

			if (!$result['success']) {
				$statusCode = $result['status_code'];

				// Map status codes to appropriate responses
				if ($statusCode === 404) {
					return $this->errorResponse(
						'Usuario no encontrado en el registro nacional',
						ApiStatuses::STATUS_NOT_FOUND
					);
				} elseif ($statusCode === 503) {
					return $this->errorResponse(
						'Servicio de registro nacional no disponible',
						ApiStatuses::STATUS_SERVICE_UNAVAILABLE
					);
				} else {
					return $this->errorResponse(
						$result['error'] ?? ErrorMessages::SERVER_ERROR,
						ApiStatuses::STATUS_INTERNAL_SERVER_ERROR
					);
				}
			}

			$data = $result['data'];

			$requiredFields = [
				$data['locus']['state'] ?? null,
				$data['locus']['city'] ?? null,
				$data['locus']['name'] ?? null,
				$data['locus']['address'] ?? null,
				$data['locus']['tableId'] ?? null,
			];

			$basicDataCondition = empty($data['firstName']) || empty($data['lastName']);
			$pollingDataCondition = count(array_filter($requiredFields)) !== count($requiredFields);

			if ($basicDataCondition && !$pollingDataCondition) {
				// Fue exitoso pero la información personal es nula
				return $this->errorResponse(
					'[POLICIA/ADRES] Servicio no disponible - Reintente más tarde',
					ApiStatuses::STATUS_SERVICE_UNAVAILABLE
				);
			}
			elseif ($pollingDataCondition && !$basicDataCondition) {
				// Fue exitoso pero la información de votación es nula
				return $this->errorResponse(
					'[RNEC] Servicio no disponible - Reintente más tarde',
					ApiStatuses::STATUS_SERVICE_UNAVAILABLE
				);
			}
			elseif ($basicDataCondition && $pollingDataCondition) {
				// Ambos conjuntos de datos son nulos
				return $this->errorResponse(
					'[SYSTEM] El usuario no fue encontrado en el registro nacional',
					ApiStatuses::STATUS_SERVICE_UNAVAILABLE
				);
			}

			return $this->successResponse(
				$data,
				'Datos de usuario obtenidos exitosamente del registro nacional'
			);

		} catch (\Exception $e) {
			Log::error('Error in getByIdentification', [
				'identification' => $identification,
				'error' => $e->getMessage(),
			]);

			return $this->errorResponse(
				ErrorMessages::SERVER_ERROR,
				ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
				$e
			);
		}
	}

	/**
	 * Display the specified resource by identification number.
	 */
	public function showByIdentification(string $identification): JsonResponse
	{
		$user = User::byIdentification($identification)->first();

		if (!$user) {
			return $this->errorResponse(
				'Usuario no encontrado',
				ApiStatuses::STATUS_NOT_FOUND
			);
		}

		return $this->successResponse(
			new UserResource($user->load($this->userRelations())),
			MessageHelper::make(self::ENTITY, 'show')
		);
	}

	/**
	 * Approve or reject multiple users.
	 */
	public function approveOrReject(ApproveUsersRequest $request): JsonResponse
	{
		try {
			(new ApproveUsersService())->execute($request->validated()['users']);

			return $this->successResponse(
				message: 'Usuarios procesados correctamente'
			);
		}
		catch (\Exception $e) {
			return $this->errorResponse(
				ErrorMessages::SERVER_ERROR,
				ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
				$e
			);
		}
	}

	/**
	 * Usuarios pertenecientes a un usuario
	 */
	public function children(UserChildrenRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$perPage = $validated['per_page'] ?? 25;

		$users = User::where('user_id', $validated['user_id'])
			->with($this->userRelations(['callCenters', 'users', 'callCenters.surveyResponses.electionType']))
			->orderBy('first_name')
			->orderBy('last_name')
			->paginate($perPage);

		return $this->successResponse(
			[
				'user' => new UserBasicDataResource(User::with('role')->find($validated['user_id'])),
				'children' => UserResource::collection($users),
			],
			MessageHelper::make(self::ENTITY, __FUNCTION__, true),
			$this->paginationMeta($users)
		);
	}

	public function usersMeeting(UserIdRequest $request): JsonResponse
	{
		$validated = $request->validated();

		/** @var \App\Models\User $user */
		$user = isset($validated['user_id']) ?
			User::with('role')->find($validated['user_id']) :
			Auth::user();
		
		$query = User::where(function ($q) use ($user) {
			$q->where('id', $user->id)
			->orWhere('id', $user->user_id)
			->orWhere(function ($q) use ($user) {
				$q->where('user_id', $user->id)
					->where('role_id', RoleEnum::LEADER->value);
			});
		})
		->with('role')
		->orderBy('first_name')
		->orderBy('last_name');

		$meta = [];

		if (isset($validated['per_page']) && !empty($validated['per_page'])) {
			$perPage = $validated['per_page'] ?? 25;
			$users = $query->paginate($perPage);
			$meta = $this->paginationMeta($users);
		}
		else {
			$users = $query->get();
		}

		return $this->successResponse(
			UserParentResource::collection($users),
			MessageHelper::make(self::ENTITY, __FUNCTION__, true),
			$meta
		);
	}
}