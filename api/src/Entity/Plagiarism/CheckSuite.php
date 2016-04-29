<?php
namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_checksuite")
 */
class CheckSuite extends Entity
{

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  Check[] @OneToMany(targetEntity="Check", mappedBy="checkSuite") */
    protected $checks;
    /** @var User @ManyToOne(targetEntity="User") */
    protected $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return Check[]
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * @param Check[] $checks
     */
    public function setChecks($checks)
    {
        $this->checks = $checks;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    
}