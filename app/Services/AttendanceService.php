<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AttendanceService
{
    public function checkIn(array $data): AttendanceRecord
    {
        $user = auth()->user();
        $location = Location::findOrFail($data['location_id']);

        // Validate geofence
        if (!$location->isWithinGeofence($data['latitude'], $data['longitude'])) {
            $distance = $location->calculateDistance($data['latitude'], $data['longitude']);
            throw new \Exception("You are outside the allowed area. Distance: {$distance}m, Allowed: {$location->geofence_radius}m");
        }

        // Process and save photo
        $photoPath = $this->savePhoto($data['photo'], 'checkin');

        // Create attendance record
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'location_id' => $data['location_id'],
            'shift_id' => $data['shift_id'],
            'attendance_date' => today(),
            'check_in_time' => now(),
            'check_in_latitude' => $data['latitude'],
            'check_in_longitude' => $data['longitude'],
            'check_in_accuracy' => $data['accuracy'],
            'check_in_photo' => $photoPath,
            'check_in_ip_address' => request()->ip(),
            'check_in_user_agent' => request()->userAgent(),
            'status' => 'present',
        ]);

        // Log the activity
        activity()
            ->causedBy($user)
            ->performedOn($attendance)
            ->withProperties([
                'location' => $location->name,
                'coordinates' => "{$data['latitude']}, {$data['longitude']}",
                'accuracy' => $data['accuracy']
            ])
            ->log('Employee checked in');

        return $attendance;
    }

    public function checkOut(array $data): AttendanceRecord
    {
        $user = auth()->user();
        $attendance = AttendanceRecord::with('location')
            ->where('id', $data['attendance_record_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $location = $attendance->location;

        // Validate geofence
        if (!$location->isWithinGeofence($data['latitude'], $data['longitude'])) {
            $distance = $location->calculateDistance($data['latitude'], $data['longitude']);
            throw new \Exception("You are outside the allowed area. Distance: {$distance}m, Allowed: {$location->geofence_radius}m");
        }

        // Process and save photo
        $photoPath = $this->savePhoto($data['photo'], 'checkout');

        // Update attendance record
        $attendance->update([
            'check_out_time' => now(),
            'check_out_latitude' => $data['latitude'],
            'check_out_longitude' => $data['longitude'],
            'check_out_accuracy' => $data['accuracy'],
            'check_out_photo' => $photoPath,
            'check_out_ip_address' => request()->ip(),
            'check_out_user_agent' => request()->userAgent(),
        ]);

        // Update status based on timing
        $this->updateAttendanceStatus($attendance);

        // Log the activity
        activity()
            ->causedBy($user)
            ->performedOn($attendance)
            ->withProperties([
                'location' => $location->name,
                'coordinates' => "{$data['latitude']}, {$data['longitude']}",
                'accuracy' => $data['accuracy']
            ])
            ->log('Employee checked out');

        return $attendance;
    }

    private function savePhoto(string $base64Photo, string $type): string
    {
        // Remove data URL prefix if present
        $base64Photo = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64Photo);
        
        // Decode base64
        $imageData = base64_decode($base64Photo);
        
        if (!$imageData) {
            throw new \Exception('Invalid image data');
        }

        // Generate unique filename
        $filename = $type . '_' . auth()->id() . '_' . now()->format('Y_m_d_H_i_s') . '_' . Str::random(8) . '.jpg';
        $path = 'attendance_photos/' . now()->format('Y/m/d') . '/' . $filename;

        // Process image with Intervention Image
        $image = Image::make($imageData);
        
        // Resize and optimize
        $image->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Add watermark with timestamp
        $image->text(now()->format('Y-m-d H:i:s'), 10, $image->height() - 10, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(12);
            $font->color('#ffffff');
            $font->align('left');
            $font->valign('bottom');
        });

        // Save to storage
        Storage::disk('public')->put($path, $image->encode('jpg', 85)->__toString());

        return $path;
    }

    private function updateAttendanceStatus(AttendanceRecord $attendance): void
    {
        $status = 'present';

        if ($attendance->isLate()) {
            $status = 'late';
        }

        if ($attendance->isEarlyDeparture()) {
            $status = $status === 'late' ? 'late' : 'early_departure';
        }

        $attendance->update(['status' => $status]);
    }
}