<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AttendanceRecord;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'attendance_record_id' => ['required', 'exists:attendance_records,id'],
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
            
            // Check if the attendance record belongs to the user
            $record = AttendanceRecord::where('id', $this->attendance_record_id)
                ->where('user_id', $user->id)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (!$record) {
                $validator->errors()->add('attendance_record_id', 'Invalid attendance record or already checked out.');
            }
        });
    }
}