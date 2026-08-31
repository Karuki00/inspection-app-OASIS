<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $fillable = [
        'asset_id',
        'inspector_id',
        'inspected_at',
        'type',
        'notes',
        'review_status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function itemChecks(): HasMany
    {
        return $this->hasMany(InspectionItemCheck::class, 'inspection_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class, 'inspection_id');
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class, 'inspection_id');
    }
}
