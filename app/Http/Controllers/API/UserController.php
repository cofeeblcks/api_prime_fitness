<?php

namespace App\Http\Controllers\API;

use App\Actions\Users\CreateUser;
use App\Actions\Users\UpdateUser;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserListRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	use ApiResponse;

	private const ENTITY = 'Usuario';

	/**
	 * Display a listing of the resource.
	 */
	public function index(UserListRequest $request): JsonResponse
	{
		$validated = $request->validated();

		/** @var User $user */
		$user = Auth::user();

		$query = User::query()
			->with(['role', 'identificationType'])
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

		return $this->successResponse(
			UserResource::collection($users),
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
				new UserResource($user->load(['role', 'identificationType'])),
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
			new UserResource($user->load(['role', 'identificationType'])),
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
				new UserResource($user->load(['role', 'identificationType'])),
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
}
