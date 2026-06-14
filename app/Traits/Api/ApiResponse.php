<?php

namespace App\Traits\Api;

use App\Constants\ApiStatuses;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    /**
     * Retorna una respuesta JSON exitosa estandarizada.
     *
     * @param  array  $data     Datos principales de la respuesta
     * @param  string $message  Mensaje opcional
     * @param  array  $meta     Información adicional opcional
     * @param  int    $code     Código HTTP de la respuesta (por defecto 200)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(
        array|Arrayable|JsonResource $data = [],
        string $message = '',
        array $meta = [],
        int $code = ApiStatuses::STATUS_OK
    ): JsonResponse
    {
        $response = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ];

        $response = $this->cleanResponse($response);

        return response()->json($response, $code);
    }

    /**
     * Retorna una respuesta JSON estandarizada para la autenticación con token.
     *
     * @param          $token Instancia del token generado o reutilizado
     * @param  string  $message  Mensaje opcional
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successAuthResponse($token, string $message = ''): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data'    => $this->getTokenData($token),
        ];

        $response = $this->cleanResponse($response);

        return response()->json($response, ApiStatuses::STATUS_OK);
    }

    /**
     * Retorna una respuesta JSON estandarizada en caso de error.
     *
     * @param  string $message  Mensaje de error a mostrar al cliente
     * @param  int    $code     Código HTTP de la respuesta (por defecto 400)
     * @param  mixed  $error    Información adicional sobre el error (opcional)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(
        string $message = '',
        int $code = ApiStatuses::STATUS_BAD_REQUEST,
        mixed $error = null
    ): JsonResponse {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];
        
        // Errores de validación (array por campo)
        if ($error instanceof ValidationException) {
            $response['errors'] = $error->errors();
            $response['code'] = $error->getCode();

        // Errores personalizados (array plano o info extra)
        } elseif (is_array($error)) {
            $response['errors'] = $error;

        // Error simple (string / excepción)
        } elseif (!is_null($error)) {
            $response['message'] = is_string($error)
                ? $error
                : $error->getMessage();
            $response['code'] = is_string($error)
                ? $error
                : $error->getCode();
        }

        return response()->json($response, $code);
    }

    /**
     * Retorna un array con la información del token para la respuesta JSON.
     *
     * @param $token Instancia del token
     * @return array Datos del token (tipo, valor y expiración)
     */
    protected function getTokenData($token): array
    {
        return [
            'token_type' => 'Bearer',
            'access_token' => $token->token,
            'expires_at' => $token->expires_at->toDateTimeString(),
        ];
    }

    /**
     * Retorna un array con la información de paginación para una respuesta JSON.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator $paginator  Instancia del paginador
     * @return array                                                   Datos de paginación (total, count, per_page, current_page, total_pages)
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
            ],
        ];
    }

    /**
     * Filtra un array de respuesta eliminando claves con valores vacíos.
     *
     * Valores vacíos considerados:
     * - Arrays vacíos []
     * - Strings vacíos ''
     * - Null
     *
     * @param  array $response  Array de respuesta a limpiar
     * @return array            Array filtrado sin valores vacíos
     */
    private function cleanResponse(array $response): array
    {
        return array_filter($response, fn($value) => !(is_array($value) && empty($value)) && $value !== '' && $value !== null);
    }
}
