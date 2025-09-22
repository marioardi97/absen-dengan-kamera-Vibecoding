<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Attendance permissions
            'view_own_attendance',
            'create_attendance',
            'view_all_attendance',
            'manage_attendance',
            'approve_corrections',
            
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Location management
            'view_locations',
            'create_locations',
            'edit_locations',
            'delete_locations',
            
            // Reports
            'view_reports',
            'export_reports',
            
            // System
            'view_audit_logs',
            'manage_system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $leaderRole = Role::firstOrCreate(['name' => 'leader']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Leader permissions
        $leaderRole->syncPermissions([
            'view_all_attendance',
            'approve_corrections',
            'view_users',
            'view_reports',
            'export_reports',
        ]);

        // Employee permissions
        $employeeRole->syncPermissions([
            'view_own_attendance',
            'create_attendance',
        ]);
    }
}