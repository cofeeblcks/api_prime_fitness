<?php

namespace App\Traits\Models;

use Illuminate\Support\Str;

trait FillModelData
{
    protected function fillData(string $model, array $data): array
    {
        $getFillables = (new $model)->getFillable();
        $response = [];
        foreach ($data as $key => $value) {
            if (in_array(Str::snake($key), $getFillables)) {
                if ( in_array(Str::snake($key), ['status', 'is_transitional']) ) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                $response[Str::snake($key)] = is_string($value) ? trim($value) : $value;
            }
        }

        return $response;
    }
}
