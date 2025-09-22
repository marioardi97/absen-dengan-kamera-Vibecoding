<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserLocation;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'integer', 'min:0'],
            'photo' => ['required', 'string'], // Base64 encoded image
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            
            // Check if user is assigned to this location and shift
            $userLocation = UserLocation::where('user_id', $user->id)
                ->where('location_id', $this->location_id)
                ->where('shift_id', $this->shift_id)
                ->exists();

            if (!$userLocation) {
                $validator->errors()->add('location_id', 'You are not assigned to this location and shift.');
            }

            // Check if already checked in today
            $existingRecord = \App\Models\AttendanceRecord::where('user_id', $user->id)
                ->where('attendance_date', today())
                ->where('shift_id', $this->shift_id)
                ->whereNotNull('check_in_time')
                ->exists();

            if ($existingRecord) {
                $validator->errors()->add('shift_id', 'You have already checked in for this shift today.');
            }
        });
    }
}