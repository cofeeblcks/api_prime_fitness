<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'color', 'status_type_id'])]
class Status extends Model
{
    use SoftDeletes;

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class);
    }
}
