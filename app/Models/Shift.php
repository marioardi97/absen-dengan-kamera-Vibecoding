<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function userLocations(): HasMany
    {
        return $this->hasMany(UserLocation::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_locations')
                    ->withPivot('location_id')
                    ->withTimestamps();
    }

    public function isCurrentShift(): bool
    {
        $now = now()->format('H:i');
        $startTime = $this->start_time->format('H:i');
        $endTime = $this->end_time->format('H:i');
        
        if ($startTime <= $endTime) {
            // Same day shift
            return $now >= $startTime && $now <= $endTime;
        } else {
            // Night shift (crosses midnight)
            return $now >= $startTime || $now <= $endTime;
        }
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }
}