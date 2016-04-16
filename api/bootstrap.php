<?php

use eu\luige\plagiarism\endpoint\Documentation;
use eu\luige\plagiarism\endpoint\PlagiarismCheck;

require __DIR__ . '/deps/autoload.php';

$config = require __DIR__ . '/config/config.php';
$app = new Slim\App($config);
$container = $app->getContainer();

$container[PlagiarismCheck::class] = function ($container) {
    return new PlagiarismCheck($container);
};
$container[Documentation::class] = function ($container) {
    return new Documentation($container);
};
