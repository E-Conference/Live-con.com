<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a role for a person in an event
 *
 *
 *  @ORM\Table(name="organization")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\OrganizationRepository")
 *
 */

class Organization
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
     * @ORM\Column(type="string", name="name")
     */
    protected $name;

     /**
     * homepage
     *
     *
     * @ORM\Column(type="string", name="homepage",nullable=true)
     */
    protected $homepage;

     /**
     * country
     *
     *
     * @ORM\Column(type="string", name="country",nullable=true)
     */
    protected $country;

     /**
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="organizations", cascade={"persist"})
     * @ORM\JoinTable(name="member",
     *     joinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="Cascade")})
     */
    private $members;

    /**
     *  Themes associated to this conference
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="organizations", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     *
     */
    protected $conference;

  

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set country
     *
     * @param string $country
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Organization
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Add members
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $members
     * @return Organization
     */
    public function addMember(\fibe\Bundle\WWWConfBundle\Entity\Person $members)
    {
        $this->members[] = $members;
    
        return $this;
    }

    /**
     * Remove members
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $members
     */
    public function removeMember(\fibe\Bundle\WWWConfBundle\Entity\Person $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Organization
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
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return Organization
     */
    public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference = null)
    {
        $this->conference = $conference;
    
        return $this;
    }

    /**
     * Get conference
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf 
     */
    public function getConference()
    {
        return $this->conference;
    }
}