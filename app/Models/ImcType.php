<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'description',
    'recommendation',
    'color',
    'min_value',
    'max_value',
])]
class ImcType extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'min_value' => 'double',
            'max_value' => 'double',
        ];
    }
}
