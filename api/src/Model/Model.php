<?php

namespace eu\luige\plagiarism\model;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Slim\Container;

abstract class Model {

    /** @var  Container */
    protected $container;
    /** @var  EntityManager */
    protected $entityManager;
    /** @var  array */
    protected $config;
    /** @var  Logger */
    protected $logger;

    /**
     * Service constructor.
     * @param $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->entityManager = $container->get(EntityManager::class);
        $this->config = $container->get('settings');
        $this->logger = $container->get(Logger::class);
    }


    public function pagedResultSet($repository, $page = 1) {
        return $repository->findBy([], ['id' => 'DESC'], $this->config['default_paging_size'], ($page - 1) * $this->config['default_paging_size']);
    }

    public function makeSureDatabaseConnectionIsOpened() {
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = $this->entityManager->create(
                $this->entityManager->getConnection(),
                $this->entityManager->getConfiguration()
            );
        }
    }

}