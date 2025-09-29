<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Music School Admin',
            'email' => 'admin@musicschool.com',
            'password' => Hash::make('password'),
            'phone' => '555-0100',
            'date_of_birth' => '1980-01-01',
            'address' => '123 Music Lane, Symphony City, SC 12345',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_phone' => '555-0911',
            'user_type' => 'admin',
            'is_enrolled' => false,
            'email_verified_at' => now(),
        ]);

        // Create an employee user
        User::create([
            'name' => 'Jane Employee',
            'email' => 'employee@musicschool.com',
            'password' => Hash::make('password'),
            'phone' => '555-0150',
            'date_of_birth' => '1985-06-15',
            'address' => '789 Staff Avenue, Symphony City, SC 12345',
            'emergency_contact_name' => 'John Employee',
            'emergency_contact_phone' => '555-0151',
            'user_type' => 'employee',
            'is_enrolled' => false,
            'email_verified_at' => now(),
        ]);

        // Create demo users for testing
        User::create([
            'name' => 'Demo Admin',
            'email' => 'demo_admin@musicschool.com',
            'password' => Hash::make('password123'),
            'phone' => '555-DEMO',
            'user_type' => 'admin',
            'is_enrolled' => false,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Demo Employee',
            'email' => 'demo_employee@musicschool.com',
            'password' => Hash::make('password123'),
            'phone' => '555-DEMO',
            'user_type' => 'employee',
            'is_enrolled' => false,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Demo Student',
            'email' => 'demo_student@musicschool.com',
            'password' => Hash::make('password123'),
            'phone' => '555-DEMO',
            'user_type' => 'student',
            'is_enrolled' => false,
            'email_verified_at' => now(),
        ]);

        // Create sample students
        $students = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'phone' => '555-0201',
                'date_of_birth' => '2005-03-15',
                'address' => '456 Harmony Street, Melody Town, MT 67890',
                'emergency_contact_name' => 'Robert Johnson',
                'emergency_contact_phone' => '555-0202',
                'user_type' => 'student',
                'is_enrolled' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
                'phone' => '555-0301',
                'date_of_birth' => '2003-07-22',
                'address' => '789 Rhythm Road, Beat City, BC 34567',
                'emergency_contact_name' => 'Mary Smith',
                'emergency_contact_phone' => '555-0302',
                'user_type' => 'student',
                'is_enrolled' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carol Davis',
                'email' => 'carol@example.com',
                'password' => Hash::make('password'),
                'phone' => '555-0401',
                'date_of_birth' => '2002-11-08',
                'address' => '321 Scale Avenue, Note Valley, NV 78901',
                'emergency_contact_name' => 'James Davis',
                'emergency_contact_phone' => '555-0402',
                'user_type' => 'student',
                'is_enrolled' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'phone' => '555-0501',
                'date_of_birth' => '2004-09-14',
                'address' => '654 Chord Circle, Harmony Hills, HH 45678',
                'emergency_contact_name' => 'Linda Wilson',
                'emergency_contact_phone' => '555-0502',
                'user_type' => 'student',
                'is_enrolled' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Emma Brown',
                'email' => 'emma@example.com',
                'password' => Hash::make('password'),
                'phone' => '555-0601',
                'date_of_birth' => '2006-12-01',
                'address' => '987 Tempo Trail, Cadence City, CC 56789',
                'emergency_contact_name' => 'Michael Brown',
                'emergency_contact_phone' => '555-0602',
                'user_type' => 'student',
                'is_enrolled' => false,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }
    }
}
