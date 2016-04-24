<?php

return [
    'settings' => [
        'application' => 'Plagiarism v1.0.0',
        'debug' => true,
        'displayErrorDetails' => true,
        'temp_folder' => '/tmp',
        'amqp' => [
            'server' => 'localhost',
            'port' => 5672,
            'username' => 'guest',
            'password' => 'guest'
        ],
        'doctrine' => [
            'debug' => true
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
        'workers' => [
            \eu\luige\plagiarism\plagiarismservice\Moss::class => 2,
            \eu\luige\plagiarism\plagiarismservice\Test::class => 2
        ]
    ]];
