<?php
namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_checksuite")
 */
class CheckSuite extends Entity {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  string @Column(type="string") */
    protected $name;
    /** @var  Check[] @OneToMany(targetEntity="Check", mappedBy="checkSuite") */
    protected $checks;
    /** @var User @ManyToOne(targetEntity="User") */
    protected $user;
    /** @var  \DateTime @Column(type="datetime") */
    protected $created;

    /**
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created) {
        $this->created = $created;
    }
   
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Check[]
     */
    public function getChecks() {
        return $this->checks;
    }

    /**
     * @param Check[] $checks
     */
    public function setChecks($checks) {
        $this->checks = $checks;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

}