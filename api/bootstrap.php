<?php

require __DIR__ . '/deps/autoload.php';

$config = [];

$app = new Slim\App($config);
$container = $app->getContainer();

$container[MimeType::class] = function () {
    return new MimeType();
};
