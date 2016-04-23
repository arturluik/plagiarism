<?php

return [
    'settings' => [
        'application' => 'Plagiarism v1.0.0',
        'debug' => true,
        'displayErrorDetails' => true,
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
        'workers' => [
            \eu\luige\plagiarism\plagiarismservices\Moss::class => 2,
            \eu\luige\plagiarism\plagiarismservices\Test::class => 2
        ]
    ]];
