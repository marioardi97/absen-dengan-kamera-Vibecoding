<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\CorrectionRequestRequest;
use App\Models\CorrectionRequest;
use App\Models\AttendanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CorrectionRequestController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        $requests = CorrectionRequest::with(['attendanceRecord.location', 'attendanceRecord.shift', 'reviewer'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('employee.correction-requests', compact('requests'));
    }

    public function store(CorrectionRequestRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $attendanceRecord = AttendanceRecord::where('id', $request->attendance_record_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Check if there's already a pending request for this record
            $existingRequest = CorrectionRequest::where('attendance_record_id', $attendanceRecord->id)
                ->where('status', 'pending')
                ->exists();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a pending correction request for this attendance record.'
                ], 400);
            }

            $correctionRequest = CorrectionRequest::create([
                'user_id' => $user->id,
                'attendance_record_id' => $attendanceRecord->id,
                'type' => $request->type,
                'requested_check_in_time' => $request->requested_check_in_time,
                'requested_check_out_time' => $request->requested_check_out_time,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // Log the activity
            activity()
                ->causedBy($user)
                ->performedOn($correctionRequest)
                ->withProperties([
                    'attendance_date' => $attendanceRecord->attendance_date,
                    'location' => $attendanceRecord->location->name,
                    'type' => $request->type
                ])
                ->log('Correction request submitted');

            return response()->json([
                'success' => true,
                'message' => 'Correction request submitted successfully!',
                'data' => $correctionRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit correction request: ' . $e->getMessage()
            ], 500);
        }
    }
}