<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'category_id',
        'location_id',
        'code',
        'name',
        'install_date',
        'service_date',
        'expiry_date',
        'condition_status',
        'status',
        'qr_code',
        'is_active',
    ];

    protected $casts = [
        'install_date' => 'date',
        'service_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'asset_id');
    }
}
