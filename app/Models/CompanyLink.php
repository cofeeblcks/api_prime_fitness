<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'username',
    'link',
    'link_type_id',
    'company_id',
])]
class CompanyLink extends Model
{
    use SoftDeletes;

    public function linkType(): BelongsTo
    {
        return $this->belongsTo(LinkType::class);
    }
}
