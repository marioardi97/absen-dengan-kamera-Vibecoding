<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\CorrectionRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Employee Routes
    Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/attendance/check-in', [AttendanceController::class, 'processCheckIn']);
        Route::post('/attendance/validate-location', [AttendanceController::class, 'validateLocation']);
        
        Route::get('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::post('/attendance/check-out', [AttendanceController::class, 'processCheckOut']);
        
        Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
        
        // Correction Requests
        Route::get('/correction-requests', [CorrectionRequestController::class, 'index'])->name('correction-requests');
        Route::post('/attendance/correction-request', [CorrectionRequestController::class, 'store']);
    });
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Placeholder routes for admin functionality
        Route::get('/employees', function () {
            return view('admin.employees');
        })->name('employees');
        
        Route::get('/locations', function () {
            return view('admin.locations');
        })->name('locations');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
        
        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('attendance');
    });
    
    // Leader Routes (placeholder)
    Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function () {
        Route::get('/dashboard', function () {
            return view('leader.dashboard');
        })->name('dashboard');
    });
});