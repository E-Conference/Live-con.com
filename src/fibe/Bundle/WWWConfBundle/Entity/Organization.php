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

    
}
