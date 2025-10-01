<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@musicschool.com',
                'phone' => '+1-555-0101',
                'bio' => 'Professional pianist with 15 years of teaching experience. Specializes in classical and contemporary styles.',
                'specialization' => 'Piano',
                'is_active' => true,
            ],
            [
                'name' => 'Mike Rodriguez',
                'email' => 'mike.rodriguez@musicschool.com',
                'phone' => '+1-555-0102',
                'bio' => 'Expert guitarist and music producer. Teaches electric and acoustic guitar for all skill levels.',
                'specialization' => 'Guitar',
                'is_active' => true,
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily.chen@musicschool.com',
                'phone' => '+1-555-0103',
                'bio' => 'Classically trained violinist with performance experience in major orchestras.',
                'specialization' => 'Violin',
                'is_active' => true,
            ],
            [
                'name' => 'David Thompson',
                'email' => 'david.thompson@musicschool.com',
                'phone' => '+1-555-0104',
                'bio' => 'Professional drummer and percussion instructor. Experience in jazz, rock, and orchestral music.',
                'specialization' => 'Drums',
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Alexander Williams',
                'email' => 'alex.williams@musicschool.com',
                'phone' => '+1-555-0105',
                'bio' => 'Music theory professor and composer. Teaches music theory, composition, and harmony.',
                'specialization' => 'Music Theory',
                'is_active' => true,
            ],
            [
                'name' => 'Marcus Jackson',
                'email' => 'marcus.jackson@musicschool.com',
                'phone' => '+1-555-0106',
                'bio' => 'Jazz saxophonist and ensemble director. Specializes in brass and woodwind instruments.',
                'specialization' => 'Saxophone',
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Parker',
                'email' => 'lisa.parker@musicschool.com',
                'phone' => '+1-555-0107',
                'bio' => 'Voice coach and choir director with extensive performance and teaching background.',
                'specialization' => 'Voice',
                'is_active' => true,
            ],
            [
                'name' => 'Jordan Smith',
                'email' => 'jordan.smith@musicschool.com',
                'phone' => '+1-555-0108',
                'bio' => 'Multi-instrumentalist specializing in bass guitar and music production techniques.',
                'specialization' => 'Bass Guitar',
                'is_active' => true,
            ],
        ];

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }
    }
}
