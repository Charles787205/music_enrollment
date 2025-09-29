<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Beginner Piano Fundamentals',
                'description' => 'Learn the basics of piano playing including proper finger positioning, basic scales, and simple melodies. Perfect for complete beginners who want to start their musical journey. This course covers reading basic sheet music, understanding rhythm, and developing proper technique.',
                'max_students' => 15,
                'current_enrolled' => 8,
                'price' => 150.00,
                'instructor' => 'Sarah Johnson',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(104), // 3 months later
                'status' => 'active',
            ],
            [
                'title' => 'Guitar Mastery Workshop',
                'description' => 'An intensive workshop designed for intermediate guitar players. Learn advanced techniques, chord progressions, and improvisation skills. Students will explore various genres including rock, jazz, and classical guitar techniques.',
                'max_students' => 20,
                'current_enrolled' => 12,
                'price' => 200.00,
                'instructor' => 'Mike Rodriguez',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(97), // 3 months later
                'status' => 'active',
            ],
            [
                'title' => 'Vocal Training Bootcamp',
                'description' => 'Comprehensive vocal training course focusing on breath control, pitch accuracy, and performance techniques. Suitable for singers of all levels who want to improve their vocal skills and stage presence.',
                'max_students' => 12,
                'current_enrolled' => 12,
                'price' => 180.00,
                'instructor' => 'Emily Chen',
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(111), // 3 months later
                'status' => 'active',
            ],
            [
                'title' => 'Drum Circle Experience',
                'description' => 'Join our community drum circle and learn various percussion instruments. This course emphasizes rhythm, coordination, and musical collaboration. No prior experience necessary - all skill levels welcome!',
                'max_students' => 25,
                'current_enrolled' => 18,
                'price' => 0.00, // Free course
                'instructor' => 'David Thompson',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(70), // 2 months later
                'status' => 'active',
            ],
            [
                'title' => 'Music Theory & Composition',
                'description' => 'Dive deep into music theory fundamentals and learn to compose your own pieces. This course covers harmony, counterpoint, form and analysis, and modern composition techniques using both traditional and digital tools.',
                'max_students' => 10,
                'current_enrolled' => 6,
                'price' => 250.00,
                'instructor' => 'Prof. Alexander Williams',
                'start_date' => now()->addDays(28),
                'end_date' => now()->addDays(118), // 3 months later
                'status' => 'active',
            ],
            [
                'title' => 'Jazz Ensemble Performance',
                'description' => 'Advanced course for experienced musicians. Learn jazz standards, improvisation techniques, and ensemble playing. Students will perform in a small combo setting and learn the art of musical conversation.',
                'max_students' => 8,
                'current_enrolled' => 7,
                'price' => 300.00,
                'instructor' => 'Marcus Jackson',
                'start_date' => now()->addDays(35),
                'end_date' => now()->addDays(125), // 3 months later
                'status' => 'active',
            ],
            [
                'title' => 'Children\'s Music Discovery',
                'description' => 'Fun and engaging music course designed specifically for children ages 6-12. Kids will explore different instruments, learn basic music concepts through games and activities, and develop their musical creativity.',
                'max_students' => 16,
                'current_enrolled' => 10,
                'price' => 120.00,
                'instructor' => 'Lisa Parker',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(74), // 2 months later
                'status' => 'active',
            ],
            [
                'title' => 'Electronic Music Production',
                'description' => 'Learn modern music production techniques using digital audio workstations (DAW). Course covers beat making, sound design, mixing, and mastering. Students will create their own electronic music tracks from start to finish.',
                'max_students' => 12,
                'current_enrolled' => 4,
                'price' => 220.00,
                'instructor' => 'Jordan Smith',
                'start_date' => now()->addDays(42),
                'end_date' => now()->addDays(132), // 3 months later
                'status' => 'active',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
