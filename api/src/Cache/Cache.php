<?php

namespace eu\luige\plagiarism\cache;

use Monolog\Logger;
use Predis\Client;
use Slim\Container;

class Cache {

    /** @var  Client */
    private $redis;
    /** @var  Container */
    private $container;
    /** @var Logger */
    private $logger;
    /** @var array */
    private $config;

    /**
     * Cache constructor.
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
        $this->redis = new Client($this->config['redis']);
        $this->config = $container->get("settings");
    }


    private function prepare() {
        if (!$this->redis->isConnected()) {
            $this->redis->connect();
        }
        return $this->redis->isConnected();
    }

    public function put($key, $value, $expire = 0) {
        if ($this->prepare()) {
            $this->logger->addInfo("[CACHE] Storing $key => " . substr($value, 0, 255) . " for $expire");
            $this->redis->set($key, $value);
            if ($expire) {
                $this->redis->expire($key, $expire);
            }
        }
    }

    public function get($key, $default = false) {
        $this->logger->addInfo("[CACHE] Getting cached key: $key");
        if ($this->prepare()) {
            $value = $this->redis->get($key);
            if ($value !== null) {
                $this->logger->info("[CACHE] got value: " . substr($value, 0, 255));
                return $value;
            }
            $this->logger->info("[CACHE] not hit");
            return $default;
        }
    }
}