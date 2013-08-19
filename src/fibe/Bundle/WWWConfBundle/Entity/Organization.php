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
     * libelle
     *
     *
     * @ORM\Column(type="string", name="libelle")
     */
    protected $libelle;

     /**
     * homepage
     *
     *
     * @ORM\Column(type="string", name="homepage")
     */
    protected $homepage;

     /**
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="organizations", cascade={"persist"})
     * @ORM\JoinTable(name="member",
     *     joinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="Cascade")})
     */
    private $members;

  

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
     * Set libelle
     *
     * @param string $libelle
     * @return Organization
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
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
}