<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AttendanceRecord;

class CorrectionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'attendance_record_id' => ['required', 'exists:attendance_records,id'],
            'type' => ['required', 'in:check_in,check_out,both'],
            'requested_check_in_time' => ['nullable', 'date', 'required_if:type,check_in,both'],
            'requested_check_out_time' => ['nullable', 'date', 'required_if:type,check_out,both'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            
            // Check if the attendance record belongs to the user
            $record = AttendanceRecord::where('id', $this->attendance_record_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$record) {
                $validator->errors()->add('attendance_record_id', 'Invalid attendance record.');
            }

            // Validate requested times are reasonable
            if ($this->requested_check_in_time) {
                $checkInTime = \Carbon\Carbon::parse($this->requested_check_in_time);
                $attendanceDate = $record ? $record->attendance_date : now();
                
                if ($checkInTime->diffInDays($attendanceDate) > 7) {
                    $validator->errors()->add('requested_check_in_time', 'Requested check-in time cannot be more than 7 days from the attendance date.');
                }
            }

            if ($this->requested_check_out_time) {
                $checkOutTime = \Carbon\Carbon::parse($this->requested_check_out_time);
                $attendanceDate = $record ? $record->attendance_date : now();
                
                if ($checkOutTime->diffInDays($attendanceDate) > 7) {
                    $validator->errors()->add('requested_check_out_time', 'Requested check-out time cannot be more than 7 days from the attendance date.');
                }
            }

            // Ensure check-out time is after check-in time
            if ($this->requested_check_in_time && $this->requested_check_out_time) {
                $checkIn = \Carbon\Carbon::parse($this->requested_check_in_time);
                $checkOut = \Carbon\Carbon::parse($this->requested_check_out_time);
                
                if ($checkOut->lte($checkIn)) {
                    $validator->errors()->add('requested_check_out_time', 'Check-out time must be after check-in time.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'requested_check_in_time.required_if' => 'Check-in time is required for this correction type.',
            'requested_check_out_time.required_if' => 'Check-out time is required for this correction type.',
            'reason.min' => 'Please provide a detailed reason (at least 10 characters).',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }
}