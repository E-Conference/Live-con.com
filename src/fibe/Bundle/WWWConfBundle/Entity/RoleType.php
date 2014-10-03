<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use fibe\Bundle\WWWConfBundle\Entity\Role;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a role for a person in an event
 *
 *
 *  @ORM\Table(name="role_type")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\RoleTypeRepository")
 *
 */

class RoleType
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * name
     *
     *
     * @ORM\Column(type="string", name="name", nullable=false)
     */
    protected $name;


    /**
     * label
     *
     *
     * @ORM\Column(type="string", name="label", nullable=false)
     */
    protected $label;

    /**
     * role
     * Role how have this type
     *  
     * @ORM\OneToMany(targetEntity="Role", mappedBy="type", cascade={"remove"})
     */
    private $roles;

    /**
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="roleTypes", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     *
     */
    private $conference;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->role = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add role
     *
     * @param Role $role
     * @return RoleType
     */
    public function addRole(Role $role)
    {
        $this->role[] = $role;
    
        return $this;
    }

    /**
     * Remove role
     *
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $this->role->removeElement($role);
    }

    /**
     * Get role
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return RoleType
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


      /**
     * Set label
     *
     * @param string $label
     * @return RoleType
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

  /**
   * Set conference
   *
   * @param WwwConf $conf
   * @return RoleType
   */
    public function setConference(WwwConf $conf)
    {
      $this->conference = $conf;

      return $this;
    }

    /**
     * Get conference
     *
     * @return WwwConf
     */
    public function getConference()
    {
      return $this->conference;
    }
}