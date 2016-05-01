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

    public function update($id, $serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads, $plagiarismServiciePayloads) {
        $preset = $this->get($id);
        if (!$preset) {
            return null;
        }
        $preset->setResourceProviderNames($resourceProviderNames);
        $preset->setResourceProviderPayloads($resourceProviderPayloads);
        $preset->setPlagiarismServicePayloads($plagiarismServiciePayloads);
        $preset->setServiceNames($serviceNames);
        $preset->setSuiteName($suiteName);

        $this->entityManager->persist($preset);
        $this->entityManager->flush();

        return $preset;
    }

    public function totalPages() {
        return ceil(count($this->presetRepository->findAll()) / $this->config['default_paging_size']);
    }

    public function create($serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads, $plagiarismServicePayloads) {
        $preset = new \eu\luige\plagiarism\entity\Preset();
        $preset->setResourceProviderNames($resourceProviderNames);
        $preset->setResourceProviderPayloads($resourceProviderPayloads);
        $preset->setPlagiarismServicePayloads($plagiarismServicePayloads);
        $preset->setServiceNames($serviceNames);
        $preset->setSuiteName($suiteName);

        $this->entityManager->persist($preset);
        $this->entityManager->flush();

        return $preset;
    }

    public function delete($id) {

        $preset = $this->get($id);
        if ($preset) {
            $this->entityManager->remove($preset);
            $this->entityManager->flush();
            return false;
        }

        return false;
    }

    public function all($page = 1) {
        return $this->pagedResultSet($this->presetRepository, $page);
    }

    /**
     * @param $id
     * @return \eu\luige\plagiarism\entity\Preset
     * @throws \Exception
     */
    public function get($id) {
        $preset = $this->presetRepository->findOneBy(['id' => $id]);
        if (!$preset) {
            throw new \Exception("No such preset: $id", 404);
        }

        return $preset;
    }
}

