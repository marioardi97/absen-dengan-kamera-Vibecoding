<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        // Get user's assigned locations and shifts
        $userLocations = UserLocation::with(['location', 'shift'])
            ->where('user_id', $user->id)
            ->get();

        // Get today's attendance records
        $todayAttendance = AttendanceRecord::with(['location', 'shift'])
            ->where('user_id', $user->id)
            ->where('attendance_date', today())
            ->get();

        // Get recent attendance history
        $recentAttendance = AttendanceRecord::with(['location', 'shift'])
            ->where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $thisMonthAttendance = AttendanceRecord::where('user_id', $user->id)
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $thisMonthPresent = AttendanceRecord::where('user_id', $user->id)
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereNotNull('check_in_time')
            ->count();

        $attendanceRate = $thisMonthAttendance > 0 ? round(($thisMonthPresent / $thisMonthAttendance) * 100, 1) : 0;

        return view('employee.dashboard', compact(
            'userLocations',
            'todayAttendance',
            'recentAttendance',
            'thisMonthAttendance',
            'thisMonthPresent',
            'attendanceRate'
        ));
    }
}