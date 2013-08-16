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
     * @OneToOne(targetEntity="person")
     * @JoinColumn(name="id_person", referencedColumnName="id")
     *
     */
    protected $id_person;

    /**
     *  
     * @OneToOne(targetEntity="organization")
     * @JoinColumn(name="id_organization, referencedColumnName="id")
     *
     */
    protected $id_organization;

}
