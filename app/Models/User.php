<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'regu',
    'badge_id',
    'email',
    'phone',
    'password',
    'role',
    'shift',
    'assigned_location_id',
    'employment_status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $casts = [
        'password' => 'hashed',
        'employment_status' => 'string',
    ];

    public function assignedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'assigned_location_id');
    }

    public function inspectionSchedules(): HasMany
    {
        return $this->hasMany(InspectionSchedule::class, 'officer_id');
    }

    public function inspectionsPerformed(): HasMany
    {
        return $this->hasMany(Inspection::class, 'inspector_id');
    }

    public function inspectionsReviewed(): HasMany
    {
        return $this->hasMany(Inspection::class, 'reviewed_by');
    }

    public function problemsReported(): HasMany
    {
        return $this->hasMany(Problem::class, 'reported_by');
    }

    public function problemsAssigned(): HasMany
    {
        return $this->hasMany(Problem::class, 'assigned_to');
    }
}
