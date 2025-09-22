<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'is_active' => true,
            ],
            [
                'name' => 'Afternoon Shift',
                'start_time' => '16:00:00',
                'end_time' => '00:00:00', // Midnight
                'is_active' => true,
            ],
            [
                'name' => 'Night Shift',
                'start_time' => '00:00:00',
                'end_time' => '08:00:00',
                'is_active' => true,
            ],
            [
                'name' => 'Security Shift',
                'start_time' => '18:00:00',
                'end_time' => '06:00:00', // Next day 6 AM
                'is_active' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['name' => $shift['name']],
                $shift
            );
        }
    }
}