<?php

use eu\luige\plagiarism\endpoint\Check;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require __DIR__ . '/deps/autoload.php';

$config = require __DIR__ . '/config/config.php';
$app = new Slim\App($config);
$container = $app->getContainer();
$config = $container->get("settings");

$log = new Logger($config['application']);
$log->pushHandler(new StreamHandler($config['monolog']['logfile'], $config['monolog']['loglevel']));

register_shutdown_function(function () use ($log) {
    $error = error_get_last();
    if ($error !== NULL) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];
        if (strstr($errfile, "capella") !== false) {
            $log->addError("Shutdown error $errno: $errstr in $errfile line $errline", ["error" => $error]);
        }
    }
});
set_exception_handler(function (Exception $e) use ($log) {
    if (strstr($e->getFile(), "capella") !== false) {
        $log->addError($e->getTraceAsString(), ["exception" => $e]);
    }
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log) {
    if (strstr($errfile, "capella") !== false) {
        $log->addError("Error $errno: $errstr in $errfile line $errline");
    }
}, E_ALL);

$container[AMQPStreamConnection::class] = function () use ($config) {
    return new AMQPStreamConnection(
        $config['amqp']['server'],
        $config['amqp']['port'],
        $config['amqp']['username'],
        $config['amqp']['password']
    );
};
$container[Logger::class] = function () use ($log) {
    return $log;
};
$container[Check::class] = function ($container) {
    return new Check($container);
};
