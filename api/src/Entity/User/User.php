<?php


namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="user_user")
 */
class User extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  @Column(type="string") */
    protected $name;
    /** @var  @Column(type="string") */
    protected $email;
    /** @var  @Column(type="string") */
    protected $authenticationProvider;
    /** @var  @Column(type="datetime") */
    protected $registered;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getAuthenticationProvider()
    {
        return $this->authenticationProvider;
    }

    /**
     * @param mixed $authenticationProvider
     */
    public function setAuthenticationProvider($authenticationProvider)
    {
        $this->authenticationProvider = $authenticationProvider;
    }

    /**
     * @return mixed
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param mixed $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
    }

}