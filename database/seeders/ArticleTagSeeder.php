<?php

namespace Database\Seeders;

use App\Models\ArticleTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Anxiety',
            'Depression',
            'Stress Management',
            'Therapy',
            'Mindfulness',
            'Coping Strategies',
            'Self-Care',
            'CBT',
            'PTSD',
            'Trauma',
            'Meditation',
            'Relaxation',
            'Sleep',
            'Relationships',
            'Communication',
            'Grief',
            'Addiction',
            'Recovery',
            'Resilience',
            'Emotional Health',
            'Mental Wellness',
            'Psychotherapy',
            'Counseling',
            'Support',
            'Healing',
        ];

        foreach ($tags as $tagName) {
            ArticleTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }
    }
}
