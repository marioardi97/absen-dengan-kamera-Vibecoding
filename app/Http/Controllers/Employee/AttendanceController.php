<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\AttendanceRecord;
use App\Models\UserLocation;
use App\Models\Location;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    public function checkIn(): View
    {
        $user = auth()->user();
        
        // Get user's assigned locations and shifts
        $userLocations = UserLocation::with(['location', 'shift'])
            ->where('user_id', $user->id)
            ->get();

        // Check if already checked in today
        $todayAttendance = AttendanceRecord::where('user_id', $user->id)
            ->where('attendance_date', today())
            ->whereNotNull('check_in_time')
            ->exists();

        return view('employee.check-in', compact('userLocations', 'todayAttendance'));
    }

    public function processCheckIn(CheckInRequest $request): JsonResponse
    {
        try {
            $result = $this->attendanceService->checkIn($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function checkOut(): View
    {
        $user = auth()->user();
        
        // Get today's attendance records where user has checked in but not checked out
        $pendingCheckouts = AttendanceRecord::with(['location', 'shift'])
            ->where('user_id', $user->id)
            ->where('attendance_date', today())
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->get();

        return view('employee.check-out', compact('pendingCheckouts'));
    }

    public function processCheckOut(CheckOutRequest $request): JsonResponse
    {
        try {
            $result = $this->attendanceService->checkOut($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Check-out successful!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function validateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $location = Location::findOrFail($request->location_id);
        $isValid = $location->isWithinGeofence($request->latitude, $request->longitude);
        $distance = $location->calculateDistance($request->latitude, $request->longitude);

        return response()->json([
            'valid' => $isValid,
            'distance' => round($distance, 2),
            'allowed_radius' => $location->geofence_radius
        ]);
    }

    public function history(Request $request): View
    {
        $user = auth()->user();
        
        $query = AttendanceRecord::with(['location', 'shift'])
            ->where('user_id', $user->id);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $locations = Location::where('is_active', true)->get();
        $userLocations = UserLocation::with('shift')
            ->where('user_id', $user->id)
            ->get();

        return view('employee.attendance-history', compact('attendance', 'locations', 'userLocations'));
    }
}