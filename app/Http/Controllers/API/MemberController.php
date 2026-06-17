<?php

namespace App\Http\Controllers\API;

use App\Actions\Users\CreateUser;
use App\Actions\Users\UpdateUser;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Enums\RoleEnum;
use App\Helpers\MessageHelper;
use App\Http\Controllers\API\Concerns\QueriesUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use App\Http\Requests\UserListRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    use ApiResponse, QueriesUsers;

    private const ENTITY = 'Miembro';

    public function index(UserListRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = $this->buildUsersQuery($validated, RoleEnum::MEMBER->value);

        [$users, $meta] = $this->paginateOrGetUsers($query, $validated);

        return $this->successResponse(
            UserResource::collection($users),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true),
            $meta
        );
    }

    public function store(MemberRequest $request): JsonResponse
    {
        try {
            $response = (new CreateUser)->execute($request->all());

            return $this->successResponse(
                new UserResource($response['user']->load(['role', 'identificationType', 'status'])),
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

    public function show(User $member): JsonResponse
    {
        return $this->successResponse(
            new UserResource($member->load(['role', 'identificationType', 'status'])),
            MessageHelper::make(self::ENTITY, __FUNCTION__)
        );
    }

    public function update(MemberRequest $request, User $member): JsonResponse
    {
        try {
            $response = (new UpdateUser)->execute($member->id, $request->all());

            return $this->successResponse(
                new UserResource($response['user']->load(['role', 'identificationType', 'status'])),
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

    public function destroy(User $member): JsonResponse
    {
        $member->delete();

        return $this->successResponse(
            message: MessageHelper::make(self::ENTITY, __FUNCTION__)
        );
    }
}
