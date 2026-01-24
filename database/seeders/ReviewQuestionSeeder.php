<?php

namespace Database\Seeders;

use App\Models\ReviewQuestion;
use App\Models\ReviewQuestionOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing questions
        ReviewQuestionOption::truncate();
        ReviewQuestion::truncate();

        // Doctor Review Questions
        $this->createDoctorQuestions();

        // Session Review Questions
        $this->createSessionQuestions();
    }

    /**
     * Create questions for doctor reviews
     */
    private function createDoctorQuestions()
    {
        $questions = [
            [
                'question_text' => 'How would you rate your overall experience with your doctor?',
                'question_type' => 'star_rating',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 1,
            ],
            [
                'question_text' => 'How would you rate your doctor\'s communication skills?',
                'question_type' => 'star_rating',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 2,
            ],
            [
                'question_text' => 'How professional was your doctor during your sessions?',
                'question_type' => 'star_rating',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 3,
            ],
            [
                'question_text' => 'Did your doctor listen to your concerns?',
                'question_type' => 'yes_no',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 4,
            ],
            [
                'question_text' => 'Do you feel comfortable discussing sensitive topics?',
                'question_type' => 'yes_no',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 5,
            ],
            [
                'question_text' => 'How effective has the treatment been?',
                'question_type' => 'star_rating',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 6,
            ],
            [
                'question_text' => 'Would you recommend your doctor to others?',
                'question_type' => 'yes_no',
                'applies_to' => 'doctor',
                'is_required' => true,
                'order' => 7,
            ],
        ];

        foreach ($questions as $questionData) {
            ReviewQuestion::create($questionData);
        }
    }

    /**
     * Create questions for session reviews
     */
    private function createSessionQuestions()
    {
        $questions = [
            [
                'question_text' => 'How helpful was this session?',
                'question_type' => 'star_rating',
                'applies_to' => 'session',
                'is_required' => true,
                'order' => 1,
            ],
            [
                'question_text' => 'How comfortable did you feel during the session?',
                'question_type' => 'star_rating',
                'applies_to' => 'session',
                'is_required' => true,
                'order' => 2,
            ],
            [
                'question_text' => 'Did you make progress on your goals?',
                'question_type' => 'yes_no',
                'applies_to' => 'session',
                'is_required' => true,
                'order' => 3,
            ],
            [
                'question_text' => 'What activities or discussions were most helpful?',
                'question_type' => 'multiple_choice',
                'applies_to' => 'session',
                'is_required' => false,
                'order' => 4,
            ],
            [
                'question_text' => 'How well did the session address your current needs?',
                'question_type' => 'star_rating',
                'applies_to' => 'session',
                'is_required' => true,
                'order' => 5,
            ],
        ];

        foreach ($questions as $questionData) {
            $question = ReviewQuestion::create($questionData);

            // Add options for multiple choice question
            if ($questionData['question_type'] === 'multiple_choice') {
                $options = [
                    ['option_text' => 'Talk therapy', 'option_value' => 'talk_therapy', 'order' => 1],
                    ['option_text' => 'Exercises', 'option_value' => 'exercises', 'order' => 2],
                    ['option_text' => 'Goal setting', 'option_value' => 'goal_setting', 'order' => 3],
                    ['option_text' => 'Coping strategies', 'option_value' => 'coping_strategies', 'order' => 4],
                    ['option_text' => 'Other', 'option_value' => 'other', 'order' => 5],
                ];

                foreach ($options as $optionData) {
                    $question->options()->create($optionData);
                }
            }
        }
    }
}
