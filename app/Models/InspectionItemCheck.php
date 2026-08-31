<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionItemCheck extends Model
{
    protected $fillable = [
        'inspection_id',
        'component_name',
        'condition_code',
        'remark',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
