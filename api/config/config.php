<?php

return [
    'settings' => [
        'application' => 'Plagiarism v1.0.0',
        'debug' => true,
        'displayErrorDetails' => true,
        'temp_folder' => '/tmp',
        'default_paging_size' => 15,
        'app_root' => __DIR__ . '/../',
        'amqp' => [
            'server' => 'localhost',
            'port' => 5672,
            'username' => 'guest',
            'password' => 'guest'
        ],
        'google' => [
            'auth_url' => 'https://www.googleapis.com/oauth2/v1/tokeninfo'
        ],
        'doctrine' => [
            'debug' => true
        ],
        "redis" => [
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6379,
        ],
        'database' => [
            'name' => 'plagiarism',
            'user' => 'plagiarism',
            'password' => 'sandbox',
            'host' => 'localhost',
            'driver' => 'pdo_pgsql'
        ],
        'monolog' => [
            'loglevel' => \Psr\Log\LogLevel::DEBUG,
            'logfile' => '/logs/app.log'
        ],
        'moss' => [
            'key' => '873311630'
        ],
        //  Weighted average is used for combining different plagiarismService percentages
        'reliability' => [
            \eu\luige\plagiarism\plagiarismservice\Moss::class => 1,
            \eu\luige\plagiarism\plagiarismservice\MockService::class => 1,
            \eu\luige\plagiarism\plagiarismservice\JPlag::class => 1
        ],
        // Automatically starts Nr of workers for plagiarsimService
        'workers' => [
            \eu\luige\plagiarism\plagiarismservice\Moss::class => 2,
            \eu\luige\plagiarism\plagiarismservice\MockService::class => 2,
            \eu\luige\plagiarism\plagiarismservice\JPlag::class => 2
        ]
    ]];
