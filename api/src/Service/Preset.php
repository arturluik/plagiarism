<?php
namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityRepository;
use Slim\Container;

class Preset extends Service {

    /** @var EntityRepository */
    private $presetRepository;

    /**
     * Preset constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->presetRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Preset::class);
    }

    public function update($id, $serviceName, $resourceProviderName, $suiteName, $resourceProviderPayload) {
        $preset = $this->getById($id);
        if(!$preset) {
            return null;
        }
        $preset->setResourceProviderName($resourceProviderName);
        $preset->setResourceProviderPayload($resourceProviderPayload);
        $preset->setServiceName($serviceName);
        $preset->setSuiteName($suiteName);

        $this->entityManager->persist($preset);
        $this->entityManager->flush();

        return $preset;
    }

    public function create($serviceName, $resourceProviderName, $suiteName, $resourceProviderPayload) {
        $preset = new \eu\luige\plagiarism\entity\Preset();
        $preset->setResourceProviderName($resourceProviderName);
        $preset->setResourceProviderPayload($resourceProviderPayload);
        $preset->setServiceName($serviceName);
        $preset->setSuiteName($suiteName);

        $this->entityManager->persist($preset);
        $this->entityManager->flush();

        return $preset;
    }

    public function delete($id) {

        $preset = $this->getById($id);
        if ($preset) {
            $this->entityManager->remove($preset);
            $this->entityManager->flush();
            return false;
        }

        return false;
    }

    public function getAll() {
        return $this->presetRepository->findAll();
    }

    public function getById($id) {
        return $this->presetRepository->findOneBy(['id' => $id]);
    }
}

