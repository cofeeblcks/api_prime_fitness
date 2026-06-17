<?php

namespace App\Http\Controllers\API;

use App\Constants\ApiStatuses;
use App\Constants\StatusesConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = User::where('email', $request->email)
                    ->where('status_id', StatusesConstants::ACTIVE)
                    ->with('role.modules')
                    ->first();

                if( $user ){

                    $token = $user->createToken('auth_token')->plainTextToken;

                    return $this->successResponse(
                        [
                            'access_token' => $token,
                            'token_type' => 'Bearer',
                            'user' => new AuthResource($user),
                        ],
                    );
                }

                return $this->errorResponse(
                    'Usuario inactivo o no es un usuario valido.',
                    ApiStatuses::STATUS_UNAUTHORIZED
                );
            }

            return $this->errorResponse(
                'Credenciales no validas.',
                ApiStatuses::STATUS_UNAUTHORIZED
            );
        }
        catch (\Exception $e) {
            return $this->errorResponse(
                'Error al iniciar sesión: ' . $e->getMessage(),
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            /**
             * Revocar SOLO el token actual (recomendado)
             */
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(
                [], 'Sesión cerrada correctamente.'
            );
        }
        catch (\Throwable $e) {
            return $this->errorResponse(
                'Error al cerrar la sesión.',
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
