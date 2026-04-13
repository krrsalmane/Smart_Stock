<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'SmartStock API',
                'description' => 'API documentation for SmartStock warehouse management system',
                'version' => '1.0.0',
                'host' => env('APP_URL', 'http://localhost'),
                'basePath' => '/api',
                'schemes' => ['http', 'https'],
                'consumes' => ['application/json'],
                'produces' => ['application/json'],
            ],
            'routes' => [
                [
                    'url' => '/api/documentation/json',
                    'name' => 'json_documentation',
                ],
            ],
            'paths' => [
                base_path('routes/api.php'),
                base_path('app/Http/Controllers'),
            ],
            'scanOptions' => [
                'exclude' => [],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'api' => 'api/documentation',
            'docs' => 'api/docs',
            'oauth2_callback' => 'api/oauth2-callback',
        ],
        'middleware' => [
            'api' => [],
            'asset' => [],
            'docs' => [],
            'oauth2_callback' => [],
        ],
        'responses' => [
            'api' => [
                'default' => 'Success',
            ],
        ],
    ],
];
