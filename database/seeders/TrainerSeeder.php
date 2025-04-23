<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Trainer;
use App\Models\TrainerSchedule;
use Illuminate\Support\Facades\Hash;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create some users who will be trainers
        $trainerUsers = [
            [
                'full_name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'mobile_number' => '09123456789',
                'gender' => 'male',
            ],
            [
                'full_name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'mobile_number' => '09987654321',
                'gender' => 'female',
            ],
            [
                'full_name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'mobile_number' => '09456789123',
                'gender' => 'male',
            ]
        ];

        foreach ($trainerUsers as $userData) {
            // Check if user already exists
            $user = User::where('email', $userData['email'])->first();
            
            if (!$user) {
                $user = User::create($userData);
            }
            
            // Create trainer profile for this user
            $trainer = Trainer::create([
                'user_id' => $user->id,
                'profile_url' => null, // Using default image
                'short_intro' => 'Professional fitness trainer with 5+ years of experience',
                'instructor_schedule' => 'Weekdays and Weekends',
                'hourly_rate' => rand(500, 1500),
                'specialization' => $userData['full_name'] === 'John Smith' ? 'Personal Trainer' : 
                                   ($userData['full_name'] === 'Sarah Johnson' ? 'Fitness Coach' : 'Boxing Instructor'),
                'instructor_for' => $userData['full_name'] === 'John Smith' ? 'gym' : 
                                   ($userData['full_name'] === 'Sarah Johnson' ? 'gym,boxing' : 'boxing,muay-thai'),
            ]);
            
            // Create schedules for this trainer
            $schedules = [
                'Monday' => ['09:00:00', '17:00:00'],
                'Tuesday' => ['09:00:00', '17:00:00'],
                'Wednesday' => ['09:00:00', '17:00:00'],
                'Thursday' => ['09:00:00', '17:00:00'],
                'Friday' => ['09:00:00', '15:00:00'],
                'Saturday' => ['10:00:00', '14:00:00'],
            ];
            
            // Slightly different schedule for Sarah
            if ($userData['full_name'] === 'Sarah Johnson') {
                $schedules = [
                    'Monday' => ['08:00:00', '16:00:00'],
                    'Tuesday' => ['08:00:00', '16:00:00'],
                    'Wednesday' => ['08:00:00', '16:00:00'],
                    'Thursday' => ['08:00:00', '16:00:00'],
                    'Friday' => ['08:00:00', '14:00:00'],
                    'Saturday' => ['09:00:00', '13:00:00'],
                ];
            }
            
            foreach ($schedules as $day => $times) {
                TrainerSchedule::create([
                    'trainer_id' => $trainer->id,
                    'day_of_week' => $day,
                    'start_time' => $times[0],
                    'end_time' => $times[1],
                ]);
            }
        }
    }
}
