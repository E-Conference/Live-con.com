<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;



/**
 * This entity define relation between person and an event
 *
 *
 *  @ORM\Table(name="role")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\RoleRepository")
 *
 */

class Role
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
     *@ORM\OneToOne(targetEntity="IDCI\Bundle\SimpleScheduleBundle\Entity\CalendarEntityRelation")
     * @ORM\JoinColumn(name="id_event_relation", referencedColumnName="id")
     *
     */
    // protected $id_event_relation;

    /**
     *  
     *@ORM\OneToOne(targetEntity="RoleType")
     * @ORM\JoinColumn(name="id_role_type", referencedColumnName="id")
     *
     */
    protected $id_role_type;

}
