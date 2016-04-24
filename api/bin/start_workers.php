<?php

require __DIR__ . '/../bootstrap.php';

$services = \eu\luige\plagiarism\plagiarismservice\PlagiarismService::getServices();
foreach ($services as $service) {
    startWorker($service);
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
