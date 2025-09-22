<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'employee_id' => 'ADM001',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0100',
                'department' => 'IT',
                'position' => 'System Administrator',
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Leader User
        $leader = User::firstOrCreate(
            ['email' => 'leader@example.com'],
            [
                'name' => 'Team Leader',
                'employee_id' => 'LDR001',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0200',
                'department' => 'Operations',
                'position' => 'Team Leader',
                'is_active' => true,
            ]
        );
        $leader->assignRole('leader');

        // Employee User
        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'John Employee',
                'employee_id' => 'EMP001',
                'password' => Hash::make('password'),
                'phone' => '+1-555-0300',
                'department' => 'Operations',
                'position' => 'Staff',
                'is_active' => true,
            ]
        );
        $employee->assignRole('employee');

        // Additional employees for testing
        $employees = [
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'employee_id' => 'EMP002',
                'department' => 'Operations',
                'position' => 'Senior Staff',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'employee_id' => 'EMP003',
                'department' => 'Security',
                'position' => 'Security Guard',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'employee_id' => 'EMP004',
                'department' => 'Maintenance',
                'position' => 'Maintenance Staff',
            ],
        ];

        foreach ($employees as $empData) {
            $emp = User::firstOrCreate(
                ['email' => $empData['email']],
                array_merge($empData, [
                    'password' => Hash::make('password'),
                    'phone' => '+1-555-' . rand(1000, 9999),
                    'is_active' => true,
                ])
            );
            $emp->assignRole('employee');
        }
    }
}