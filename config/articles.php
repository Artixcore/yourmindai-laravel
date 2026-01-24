<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Article Revenue Settings
    |--------------------------------------------------------------------------
    */
    'revenue' => [
        'base_rate_per_thousand' => 0.50, // $0.50 per 1000 views
        
        'quality_multipliers' => [
            'seo_threshold' => 80,
            'seo_multiplier' => 1.5,
            
            'readability_threshold' => 70,
            'readability_multiplier' => 1.2,
            
            'featured_multiplier' => 2.0,
            
            'likes_threshold' => 50,
            'likes_multiplier' => 1.3,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    */
    'upload' => [
        'max_image_size' => 2048, // KB (2MB)
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
    ],
];
