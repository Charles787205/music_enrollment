<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instruments = [
            // String Instruments
            [
                'name' => 'Acoustic Guitar',
                'description' => 'Perfect for beginners and professionals alike. Learn classical, folk, and modern techniques.',
                'category' => 'string',
                'difficulty_level' => 'beginner',
                'rental_fee' => 25.00,
                'is_available' => true,
            ],
            [
                'name' => 'Electric Guitar',
                'description' => 'Rock, blues, and jazz guitar for intermediate and advanced players.',
                'category' => 'string',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 35.00,
                'is_available' => true,
            ],
            [
                'name' => 'Violin',
                'description' => 'Classical string instrument requiring precision and dedication.',
                'category' => 'string',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 40.00,
                'is_available' => true,
            ],
            [
                'name' => 'Bass Guitar',
                'description' => 'Foundation of rhythm section in bands and ensembles.',
                'category' => 'string',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 30.00,
                'is_available' => true,
            ],
            [
                'name' => 'Cello',
                'description' => 'Deep, rich tones perfect for classical and contemporary music.',
                'category' => 'string',
                'difficulty_level' => 'advanced',
                'rental_fee' => 60.00,
                'is_available' => true,
            ],

            // Wind Instruments
            [
                'name' => 'Flute',
                'description' => 'Graceful woodwind instrument with bright, clear tones.',
                'category' => 'wind',
                'difficulty_level' => 'beginner',
                'rental_fee' => 20.00,
                'is_available' => true,
            ],
            [
                'name' => 'Clarinet',
                'description' => 'Versatile woodwind suitable for classical, jazz, and folk music.',
                'category' => 'wind',
                'difficulty_level' => 'beginner',
                'rental_fee' => 25.00,
                'is_available' => true,
            ],
            [
                'name' => 'Saxophone',
                'description' => 'Popular in jazz, blues, and contemporary music.',
                'category' => 'wind',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 45.00,
                'is_available' => true,
            ],
            [
                'name' => 'Oboe',
                'description' => 'Expressive woodwind with distinctive nasal tone.',
                'category' => 'wind',
                'difficulty_level' => 'advanced',
                'rental_fee' => 50.00,
                'is_available' => true,
            ],

            // Brass Instruments
            [
                'name' => 'Trumpet',
                'description' => 'Bright, powerful brass instrument perfect for beginners.',
                'category' => 'brass',
                'difficulty_level' => 'beginner',
                'rental_fee' => 30.00,
                'is_available' => true,
            ],
            [
                'name' => 'Trombone',
                'description' => 'Slide brass instrument with rich, warm tones.',
                'category' => 'brass',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 35.00,
                'is_available' => true,
            ],
            [
                'name' => 'French Horn',
                'description' => 'Elegant brass instrument used in orchestras and chamber music.',
                'category' => 'brass',
                'difficulty_level' => 'advanced',
                'rental_fee' => 55.00,
                'is_available' => true,
            ],
            [
                'name' => 'Tuba',
                'description' => 'Foundation of the brass section with deep, resonant bass.',
                'category' => 'brass',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 65.00,
                'is_available' => true,
            ],

            // Percussion Instruments
            [
                'name' => 'Drum Set',
                'description' => 'Complete acoustic drum kit for rock, jazz, and popular music.',
                'category' => 'percussion',
                'difficulty_level' => 'beginner',
                'rental_fee' => 50.00,
                'is_available' => true,
            ],
            [
                'name' => 'Xylophone',
                'description' => 'Bright, percussive mallet instrument perfect for beginners.',
                'category' => 'percussion',
                'difficulty_level' => 'beginner',
                'rental_fee' => 25.00,
                'is_available' => true,
            ],
            [
                'name' => 'Timpani',
                'description' => 'Professional orchestral kettle drums.',
                'category' => 'percussion',
                'difficulty_level' => 'advanced',
                'rental_fee' => 70.00,
                'is_available' => true,
            ],
            [
                'name' => 'Marimba',
                'description' => 'Large wooden mallet instrument with warm, resonant tones.',
                'category' => 'percussion',
                'difficulty_level' => 'intermediate',
                'rental_fee' => 55.00,
                'is_available' => true,
            ],

            // Keyboard Instruments
            [
                'name' => 'Piano',
                'description' => 'Classic acoustic piano - the foundation of musical education.',
                'category' => 'keyboard',
                'difficulty_level' => 'beginner',
                'rental_fee' => 75.00,
                'is_available' => true,
            ],
            [
                'name' => 'Digital Piano',
                'description' => 'Modern digital piano with weighted keys and multiple sounds.',
                'category' => 'keyboard',
                'difficulty_level' => 'beginner',
                'rental_fee' => 45.00,
                'is_available' => true,
            ],
            [
                'name' => 'Organ',
                'description' => 'Classical pipe organ for advanced students.',
                'category' => 'keyboard',
                'difficulty_level' => 'advanced',
                'rental_fee' => 80.00,
                'is_available' => false, // Currently unavailable
            ],
        ];

        foreach ($instruments as $instrument) {
            Instrument::create($instrument);
        }
    }
}
