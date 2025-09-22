<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Office',
                'address' => '123 Business St, Downtown, NY 10001',
                'latitude' => 40.7589, // New York City coordinates
                'longitude' => -73.9851,
                'geofence_radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Warehouse A',
                'address' => '456 Industrial Ave, Brooklyn, NY 11201',
                'latitude' => 40.6892,
                'longitude' => -73.9742,
                'geofence_radius' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Branch Office',
                'address' => '789 Corporate Blvd, Queens, NY 11354',
                'latitude' => 40.7505,
                'longitude' => -73.8370,
                'geofence_radius' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Security Gate',
                'address' => '321 Gate Dr, Bronx, NY 10451',
                'latitude' => 40.8176,
                'longitude' => -73.9782,
                'geofence_radius' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}