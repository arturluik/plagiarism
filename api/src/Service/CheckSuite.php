<?php
namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityRepository;
use Slim\Container;

class CheckSuite extends Service {

    /** @var  EntityRepository */
    private $checkRepository;

    /**
     * CheckSuite constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\CheckSuite::class);
    }

    public function all($page = 1) {
        $result = $this->pagedResultSet($this->checkRepository, $page);
        foreach ($result as $checkSuite) {
            $this->loadDependencies($checkSuite);
        }
        return $result;
    }

    public function get($id) {
        $result = $this->checkRepository->findOneBy(['id' => $id]);
        if (!$result) {
            throw new \Exception("No such checkSuite: $id", 404);
        }
        $this->loadDependencies($result);
        return $result;
    }

    /**
     * @param \eu\luige\plagiarism\entity\CheckSuite $checkSuite
     */
    private function loadDependencies($checkSuite) {
        $checkSuite->setChecks($checkSuite->getChecks()->toArray());
    }

    /**
     * @param string $name
     * @return \eu\luige\plagiarism\entity\CheckSuite
     */
    public function create($name) {

        $checkSuite = new \eu\luige\plagiarism\entity\CheckSuite();
        $checkSuite->setUser(null);
        $checkSuite->setName($name);
        $checkSuite->setCreated(new \DateTime());

        $this->entityManager->persist($checkSuite);
        $this->entityManager->flush();

        return $checkSuite;
    }

}