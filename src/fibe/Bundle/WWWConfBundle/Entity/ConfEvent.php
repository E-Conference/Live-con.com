<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 

use IDCI\Bundle\SimpleScheduleBundle\Entity\Event; 

/**
 * ConfEvent
 *
 * @ORM\Entity  
 * @ORM\Entity(repositoryClass="IDCI\Bundle\SimpleScheduleBundle\Repository\EventRepository")
 */
class ConfEvent extends Event
{
    
    /**
     * wwwConf
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confEvents", cascade={"persist"})
     * @ORM\JoinColumn(name="wwwConf_id", referencedColumnName="id")
     */
    private $wwwConf;
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
	    parent::__construct();
    }
    
    
    /**
     * Set wwwConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf
     * @return ConfEvent
     */
    public function setWwwConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $wwwConf = null)
    {
        $this->wwwConf = $wwwConf;
    
        return $this;
    }

    /**
     * Get wwwConf
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf 
     */
    public function getWwwConf()
    {
        return $this->wwwConf;
    }  
}
