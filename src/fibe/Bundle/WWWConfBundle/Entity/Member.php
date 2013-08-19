<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\Organization;


/**
 * This entity define relation between person and an organization
 *
 *
 *  @ORM\Table(name="member")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\MemberRepository")
 *
 */

class Member
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    
    /**
     *  
     *@ORM\OneToOne(targetEntity="person")
     * @ORM\JoinColumn(name="id_person", referencedColumnName="id")
     *
     */
    protected $id_person;

    /**
     *  
     *@ORM\OneToOne(targetEntity="organization")
     * @ORM\JoinColumn(name="id_organization", referencedColumnName="id")
     *
     */
    protected $id_organization;


   

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
     * Set id_person
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\person $idPerson
     * @return Member
     */
    public function setIdPerson(\fibe\Bundle\WWWConfBundle\Entity\person $idPerson = null)
    {
        $this->id_person = $idPerson;
    
        return $this;
    }

    /**
     * Get id_person
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\person 
     */
    public function getIdPerson()
    {
        return $this->id_person;
    }

    /**
     * Set id_organization
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\organization $idOrganization
     * @return Member
     */
    public function setIdOrganization(\fibe\Bundle\WWWConfBundle\Entity\organization $idOrganization = null)
    {
        $this->id_organization = $idOrganization;
    
        return $this;
    }

    /**
     * Get id_organization
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\organization 
     */
    public function getIdOrganization()
    {
        return $this->id_organization;
    }
}