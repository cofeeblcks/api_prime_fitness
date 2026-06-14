<?php

namespace App\Constants;

class ErrorMessages
{
    public const SERVER_ERROR = 'Error interno del servidor';

    public const BAD_REQUEST = 'Petición inválida. Verifica los parámetros enviados';

    public const VALIDATION_ERROR = 'Los datos proporcionados no son válidos';

    public const FORBIDDEN = 'No tienes permisos para realizar esta acción';

    public const NOT_FOUND = 'Recurso no encontrado';

    public const METHOD_NOT_ALLOWED = 'Método HTTP no permitido para esta ruta';

    public const UNAUTHENTICATED = 'No autenticado';

    public const FILE_NOT_GENERATED = 'No se pudo procesar el archivo';

    public const FILE_NOT_FOUND = 'No se pudo encontrar el archivo';

    public const UNAUTHORIZED = 'No autorizado';
}
