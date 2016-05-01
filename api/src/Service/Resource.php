<?php
namespace eu\luige\plagiarism\service;


use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\resource\File;
use Slim\Container;

class Resource extends Service {

    /** @var EntityRepository */
    protected $resourceRepository;

    /**
     * Resource constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->resourceRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Resource::class);
    }

    /**
     * @param \eu\luige\plagiarism\resource\Resource $resource
     * @return \eu\luige\plagiarism\entity\Resource|Resource
     */
    public function getResourceEntity(\eu\luige\plagiarism\resource\Resource $resource) {
        if ($resource instanceof File) {
            $hash = hash('sha256', $resource->getContent());
            /** @var Resource $result */
            $result = $this->resourceRepository->findOneBy(['hash' => $hash]);
            if (!$result) {
                $resourceEntity = new \eu\luige\plagiarism\entity\Resource();
                $resourceEntity->setContent($resource->getContent());
                $resourceEntity->setHash($hash);
                $resourceEntity->setName($resource->getFileName());
                $this->entityManager->persist($resourceEntity);
                return $resourceEntity;
            } else {
                return $result;
            }
        }
        $this->logger->error("Unexpected resource found");
    }
}