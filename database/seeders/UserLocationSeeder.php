<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use App\Models\Shift;
use App\Models\UserLocation;
use Illuminate\Database\Seeder;

class UserLocationSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::role('employee')->get();
        $locations = Location::all();
        $shifts = Shift::all();

        if ($employees->count() === 0 || $locations->count() === 0 || $shifts->count() === 0) {
            return;
        }

        // Assign specific locations and shifts to employees
        $assignments = [
            'employee@example.com' => [
                ['location' => 'Main Office', 'shift' => 'Morning Shift'],
                ['location' => 'Branch Office', 'shift' => 'Afternoon Shift'],
            ],
            'jane@example.com' => [
                ['location' => 'Main Office', 'shift' => 'Morning Shift'],
                ['location' => 'Warehouse A', 'shift' => 'Morning Shift'],
            ],
            'mike@example.com' => [
                ['location' => 'Security Gate', 'shift' => 'Security Shift'],
                ['location' => 'Main Office', 'shift' => 'Night Shift'],
            ],
            'sarah@example.com' => [
                ['location' => 'Warehouse A', 'shift' => 'Morning Shift'],
                ['location' => 'Warehouse A', 'shift' => 'Afternoon Shift'],
            ],
        ];

        foreach ($assignments as $email => $userAssignments) {
            $user = $employees->firstWhere('email', $email);
            if (!$user) continue;

            foreach ($userAssignments as $assignment) {
                $location = $locations->firstWhere('name', $assignment['location']);
                $shift = $shifts->firstWhere('name', $assignment['shift']);

                if ($location && $shift) {
                    UserLocation::firstOrCreate([
                        'user_id' => $user->id,
                        'location_id' => $location->id,
                        'shift_id' => $shift->id,
                    ]);
                }
            }
        }

        // Assign remaining employees to random locations and shifts
        $unassignedEmployees = $employees->filter(function ($employee) use ($assignments) {
            return !array_key_exists($employee->email, $assignments);
        });

        foreach ($unassignedEmployees as $employee) {
            $randomLocation = $locations->random();
            $randomShift = $shifts->random();

            UserLocation::firstOrCreate([
                'user_id' => $employee->id,
                'location_id' => $randomLocation->id,
                'shift_id' => $randomShift->id,
            ]);
        }
    }
}