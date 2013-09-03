<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;



/**
 * Define the themes for confEvent
 *
 *
 *  @ORM\Table(name="theme")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\ThemeRepository")
 *
 */

class Theme
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
    
    /**
    *   Libelle
    *   @ORM\Column(type="string", name="libelle")
    */
    private $libelle;

     /**
     * confEvents
     * Events related to a theme
     *
     * @ORM\ManyToMany(targetEntity="ConfEvent", mappedBy="themes", cascade={"persist"})
     */
    private $confEvents;


     

    


   

}