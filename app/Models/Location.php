<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'parent_location_id',
        'name',
        'type',
        'building_code',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_location_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_location_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function assignedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'assigned_location_id');
    }

    public function patrolSchedules(): HasMany
    {
        return $this->hasMany(InspectionSchedule::class, 'patrol_zone_id');
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }
}
