<?php

namespace Pastel\PlatformBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pastel_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="text", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="text", length=255)
     */
    private $lastName; 
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="pastelMember", type="boolean")
     */
    private $pastelMember;
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->roles[] = 'ROLE_USER';
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set pastelMember
     *
     * @param boolean $pastelMember
     *
     * @return User
     */
    public function setPastelMember($pastelMember)
    {
        $this->pastelMember = $pastelMember;

        return $this;
    }

    /**
     * Get pastelMember
     *
     * @return boolean
     */
    public function getPastelMember()
    {
        return $this->pastelMember;
    }
}
