<?php

require __DIR__ . '/../bootstrap.php';

// Find all plagiarism services
$classMap = require __DIR__ . '/../deps/composer/autoload_classmap.php';
foreach ($classMap as $class => $path) {
    if (preg_match('/plagiarismservices/', $class) && $class != \eu\luige\plagiarism\plagiarismservices\PlagiarismService::class) {
        startWorker($class);
    }
}

function startWorker($service)
{
    global $container, $log;
    $workerCounts = $container->get("settings")["workers"];
    if (isset($workerCounts[$service])) {
        $log->info("Starting $workerCounts[$service] workers for $service");
        $bootstrap = str_replace("\\", "/", __DIR__ . '/../bootstrap.php');
        $exec = ("php " . __DIR__ . "/_run.php $bootstrap \"$service\"  1>/dev/null 2>/dev/null &");
        for ($i = $workerCounts[$service]; $i > 0; $i--) {
            exec($exec);
        }
    }
}
