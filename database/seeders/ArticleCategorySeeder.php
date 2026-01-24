<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mental Health',
                'slug' => 'mental-health',
                'description' => 'Articles about mental health conditions, treatment, and wellness',
                'icon' => 'brain',
                'order' => 1,
            ],
            [
                'name' => 'Therapy Techniques',
                'slug' => 'therapy-techniques',
                'description' => 'Various therapeutic approaches and techniques',
                'icon' => 'lightbulb',
                'order' => 2,
            ],
            [
                'name' => 'Patient Care',
                'slug' => 'patient-care',
                'description' => 'Tips and guides for patient care and support',
                'icon' => 'heart',
                'order' => 3,
            ],
            [
                'name' => 'Medical Research',
                'slug' => 'medical-research',
                'description' => 'Latest research and findings in mental health',
                'icon' => 'microscope',
                'order' => 4,
            ],
            [
                'name' => 'Wellness Tips',
                'slug' => 'wellness-tips',
                'description' => 'Practical wellness and self-care advice',
                'icon' => 'sun',
                'order' => 5,
            ],
            [
                'name' => 'Success Stories',
                'slug' => 'success-stories',
                'description' => 'Inspiring recovery and success stories',
                'icon' => 'trophy',
                'order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::create($category);
        }
    }
}
