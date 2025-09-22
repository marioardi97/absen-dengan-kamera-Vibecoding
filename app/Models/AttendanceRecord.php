<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'shift_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_accuracy',
        'check_out_accuracy',
        'check_in_photo',
        'check_out_photo',
        'check_in_ip_address',
        'check_out_ip_address',
        'check_in_user_agent',
        'check_out_user_agent',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'check_in_latitude' => 'decimal:8',
            'check_in_longitude' => 'decimal:8',
            'check_out_latitude' => 'decimal:8',
            'check_out_longitude' => 'decimal:8',
            'check_in_accuracy' => 'integer',
            'check_out_accuracy' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function getWorkedHoursAttribute(): ?string
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in_time);
        $checkOut = Carbon::parse($this->check_out_time);
        
        $diff = $checkIn->diff($checkOut);
        
        return sprintf('%d:%02d', $diff->h + ($diff->days * 24), $diff->i);
    }

    public function isLate(): bool
    {
        if (!$this->check_in_time || !$this->shift) {
            return false;
        }

        $checkInTime = Carbon::parse($this->check_in_time);
        $shiftStartTime = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->shift->start_time->format('H:i:s'));
        
        return $checkInTime->gt($shiftStartTime->addMinutes(15)); // 15 minutes grace period
    }

    public function isEarlyDeparture(): bool
    {
        if (!$this->check_out_time || !$this->shift) {
            return false;
        }

        $checkOutTime = Carbon::parse($this->check_out_time);
        $shiftEndTime = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $this->shift->end_time->format('H:i:s'));
        
        // Handle night shifts
        if ($this->shift->end_time->lt($this->shift->start_time)) {
            $shiftEndTime->addDay();
        }
        
        return $checkOutTime->lt($shiftEndTime->subMinutes(15)); // 15 minutes before shift end
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo ? asset('storage/' . $this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        return $this->check_out_photo ? asset('storage/' . $this->check_out_photo) : null;
    }
}