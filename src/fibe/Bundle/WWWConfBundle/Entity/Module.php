<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * This entity define actives module for a conference
 *
 *
 *  @ORM\Table(name="module")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\ModuleRepository")
 *
 */

class Module
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
    /**
     *  
     * @ORM\Column(type="boolean",options={"default" = 1})
     *
     */
    private $paperModule;

    /**
     * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf")
     **/
    private $conference; 


   

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
     * Set paperModule
     *
     * @param boolean $paperModule
     * @return Module
     */
    public function setPaperModule($paperModule)
    {
        $this->paperModule = $paperModule;
    
        return $this;
    }

    /**
     * Get paperModule
     *
     * @return boolean 
     */
    public function getPaperModule()
    {
        return $this->paperModule;
    }

    /**
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return Module
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