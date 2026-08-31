<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionSchedule extends Model
{
    protected $fillable = [
        'schedule_code',
        'officer_id',
        'patrol_zone_id',
        'scheduled_date',
        'shift_start',
        'shift_end',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'shift_start' => 'datetime:H:i:s',
        'shift_end' => 'datetime:H:i:s',
    ];

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function patrolZone(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'patrol_zone_id');
    }
}
