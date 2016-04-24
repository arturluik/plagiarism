<?php

namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityManager;
use Slim\Container;

abstract class Service
{

    /** @var  Container */
    protected $container;
    /** @var  EntityManager */
    protected $entityManager;
    /** @var  array */
    protected $config;

    /**
     * Service constructor.
     * @param $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->entityManager = $container->get(EntityManager::class);
        $this->config = $container->get('settings');
    }


}