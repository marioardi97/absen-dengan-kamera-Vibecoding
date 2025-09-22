<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Location;
use App\Models\AttendanceRecord;
use App\Models\CorrectionRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Overview Statistics
        $totalEmployees = User::role('employee')->where('is_active', true)->count();
        $totalLocations = Location::where('is_active', true)->count();
        
        // Today's attendance
        $todayAttendance = AttendanceRecord::whereDate('attendance_date', today())->count();
        $todayPresent = AttendanceRecord::whereDate('attendance_date', today())
            ->whereNotNull('check_in_time')->count();
        $todayPendingCheckouts = AttendanceRecord::whereDate('attendance_date', today())
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')->count();

        // This month statistics
        $thisMonthAttendance = AttendanceRecord::whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)->count();
        $thisMonthPresent = AttendanceRecord::whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->whereNotNull('check_in_time')->count();

        // Pending correction requests
        $pendingCorrections = CorrectionRequest::where('status', 'pending')->count();

        // Recent activity
        $recentAttendance = AttendanceRecord::with(['user', 'location', 'shift'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Attendance trends (last 7 days)
        $attendanceTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = AttendanceRecord::whereDate('attendance_date', $date)
                ->whereNotNull('check_in_time')
                ->count();
            $attendanceTrends[] = [
                'date' => $date->format('M j'),
                'count' => $count
            ];
        }

        // Location-wise attendance today
        $locationStats = Location::with(['attendanceRecords' => function($query) {
                $query->whereDate('attendance_date', today())
                      ->whereNotNull('check_in_time');
            }])
            ->where('is_active', true)
            ->get()
            ->map(function($location) {
                return [
                    'name' => $location->name,
                    'present' => $location->attendanceRecords->count(),
                    'capacity' => $location->users()->count()
                ];
            });

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalLocations',
            'todayAttendance',
            'todayPresent',
            'todayPendingCheckouts',
            'thisMonthAttendance',
            'thisMonthPresent',
            'pendingCorrections',
            'recentAttendance',
            'attendanceTrends',
            'locationStats'
        ));
    }
}