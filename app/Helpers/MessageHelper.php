<?php

namespace App\Helpers;


class MessageHelper
{
    private const BASE_SINGULAR = '%s %s correctamente';
    private const BASE_PLURAL   = '%s %s correctamente';

    // Acciones disponibles con su forma masculina y femenina
    private const ACTIONS = [
        'index'   => ['female' => 'obtenida',    'male' => 'obtenido'],
        'store'   => ['female' => 'creada',      'male' => 'creado'],
        'show'    => ['female' => 'obtenida',    'male' => 'obtenido'],
        'update'  => ['female' => 'actualizada', 'male' => 'actualizado'],
        'destroy' => ['female' => 'eliminada',   'male' => 'eliminado'],
    ];


    /**
     * Genera un mensaje dinámico según la entidad, acción, número (singular/plural) y género (masculino/femenino).
     *
     * @param  string  $entity   Nombre de la entidad (ej: "Área", "Usuario").
     * @param  string  $action   Acción realizada (index, store, show, update, destroy).
     * @param  bool    $plural   Indica si el mensaje debe estar en plural.
     * @param  bool    $isMale   Define el género gramatical (true = masculino, false = femenino).
     * @return string            Mensaje formateado listo para mostrar.
     */
    public static function make(string $entity, string $action, bool $plural = false, bool $isMale = true): string
    {
        // Determinar el género según el parámetro recibido
        $gender = $isMale ? 'male' : 'female';

        // Obtener el verbo correcto para la acción y género, o usar uno por defecto
        $verb   = self::ACTIONS[$action][$gender] ?? ($isMale ? 'procesado' : 'procesada');

        // Si es plural, pluralizar tanto la entidad como el verbo
        if ($plural) {
            $entity = self::pluralize($entity);
            $verb   = self::pluralize($verb);
        }

        // Seleccionar la plantilla base (singular o plural)
        $template = $plural ? self::BASE_PLURAL : self::BASE_SINGULAR;

        // Construir y devolver el mensaje formateado
        return sprintf($template, $entity, $verb);
    }

    // Regla simple para pluralizar en español
    private static function pluralize(string $word): string
    {
        $lastChar = mb_substr($word, -1);

        if (in_array(mb_strtolower($lastChar), ['a', 'e', 'i', 'o', 'u'])) {
            return $word . 's';
        }

        return $word . 'es';
    }
}
