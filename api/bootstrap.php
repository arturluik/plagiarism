<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
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
set_exception_handler(function (Throwable $e) use ($log) {
    if (strstr($e->getFile(), "capella") !== false) {
        $log->addError($e->getTraceAsString(), ["exception" => $e]);
    }
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log) {
    if (strstr($errfile, "capella") !== false) {
        $log->addError("Error $errno: $errstr in $errfile line $errline");
    }
}, E_ALL);

// Create a simple "default" Doctrine ORM configuration for Annotations
$doctrineConfig = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/src/Entity'], $config['doctrine']['devmode']);
// database configuration parameters
$conn = [
    'dbname' => $config['database']['name'],
    'user' => $config['database']['user'],
    'password' => $config['database']['password'],
    'host' => $config['database']['host'],
    'driver' => $config['database']['driver']
];

$entityManager = EntityManager::create($conn, $doctrineConfig);


$container[AMQPStreamConnection::class] = function () use ($config) {
    return new AMQPStreamConnection(
        $config['amqp']['server'],
        $config['amqp']['port'],
        $config['amqp']['username'],
        $config['amqp']['password']
    );
};

$singletons = [];
function singleton($class) {
    global $singletons, $container;
    if (!isset($singletons[$class])) {
        $singletons[$class] = new $class($container);
    }
    return $singletons[$class];
}

$jsonHelpers = new JsonHelpers\JsonHelpers($app->getContainer());
$jsonHelpers->registerResponseView();
$jsonHelpers->registerErrorHandlers();

$container[Logger::class] = function () use ($log) {
    return $log;
};
$container[Check::class] = function ($container) {
    return new Check($container);
};
$container[EntityManager::class] = function () use ($entityManager) {
    return $entityManager;
};

$singletonClasses = [
    \eu\luige\plagiarism\model\Check::class,
    \eu\luige\plagiarism\model\PathPatternMatcher::class,
    \eu\luige\plagiarism\cache\Cache::class,
    \eu\luige\plagiarism\model\Preset::class,
    \eu\luige\plagiarism\model\CheckSuite::class,
    \eu\luige\plagiarism\model\Resource::class,
    \eu\luige\plagiarism\model\Similarity::class
];

foreach ($singletonClasses as $singletonClass) {
    $container[$singletonClass] = function () use ($singletonClass) {
        return singleton($singletonClass);
    };
}
