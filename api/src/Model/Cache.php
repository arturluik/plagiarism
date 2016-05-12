<?php

namespace eu\luige\plagiarism\model;

use Predis\Client;
use Slim\Container;

class Cache extends Model
{

    /** @var  Client */
    private $redis;

    /**
     * Cache constructor.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->redis = new Client($this->config['redis']);
    }


    private function prepare()
    {
        if (!$this->redis->isConnected()) {
            $this->redis->connect();
        }
        return $this->redis->isConnected();
    }

    public function put($key, $value, $expire = 0)
    {
        if ($this->prepare()) {
            $this->logger->addInfo("[CACHE] Storing $key => $value for $expire");
            $this->redis->set($key, $value);
            if ($expire) {
                $this->redis->expire($key, $expire);
            }
        }
    }

    public function get($key, $default = false)
    {
        if ($this->prepare()) {
            $value = $this->redis->get($key);
            if ($value !== null) {
                return $value;
            }
            return $default;
        }
    }
}