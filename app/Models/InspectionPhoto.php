<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionPhoto extends Model
{
    protected $fillable = [
        'inspection_id',
        'file_path',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
