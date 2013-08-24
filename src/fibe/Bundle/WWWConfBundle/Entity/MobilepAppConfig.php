<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * This entity define a MobileAppConfig
 *
 *  @ORM\Table(name="MobileAppConfig")
 * @ORM\Entity
 *
 */

class MobileAppConfig
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * BGContent
     *
     *
     * @ORM\Column(type="string", name="BGContent")
     */
    protected $BGColorContent;

     /**
     * BGHeader
     *
     *
     * @ORM\Column(type="string", name="BGHeader")
     */
    protected $BGColorHeader;

     /**
     * BGNavBar
     *
     *
     * @ORM\Column(type="string", name="BGNavBar")
     */
    protected $BGColorNavBar;


     /**
     * BGfooter
     *
     *
     * @ORM\Column(type="string", name="BGfooter")
     */
    protected $BGColorfooter;


     /**
     * ColorContentTitle
     *
     *
     * @ORM\Column(type="string", name="ColorContentTitle")
     */
    protected $ColorContentTitle;

      /**
     * ColorHeaderTitle
     *
     *
     * @ORM\Column(type="string", name="ColorHeaderTitle")
     */
    protected $ColorHeaderTitle;


      /**
     * ColorNavBarTitle
     *
     *
     * @ORM\Column(type="string", name="ColorNavBarTitle")
     */
    protected $ColorNavBarTitle;


    /**
     * Conference related to this config
     *
     * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\wwwConf", cascade={"persist"})
     */
    private $Conference;


     /**
     * IsPublished
     *
     *
     * @ORM\Column(type="boolean", name="IsPublished")
     */
    protected $IsPublished;
        



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
     * Set BGColorContent
     *
     * @param string $bGColorContent
     * @return MobileAppConfig
     */
    public function setBGColorContent($bGColorContent)
    {
        $this->BGColorContent = $bGColorContent;
    
        return $this;
    }

    /**
     * Get BGColorContent
     *
     * @return string 
     */
    public function getBGColorContent()
    {
        return $this->BGColorContent;
    }

    /**
     * Set BGColorHeader
     *
     * @param string $bGColorHeader
     * @return MobileAppConfig
     */
    public function setBGColorHeader($bGColorHeader)
    {
        $this->BGColorHeader = $bGColorHeader;
    
        return $this;
    }

    /**
     * Get BGColorHeader
     *
     * @return string 
     */
    public function getBGColorHeader()
    {
        return $this->BGColorHeader;
    }

    /**
     * Set BGColorNavBar
     *
     * @param string $bGColorNavBar
     * @return MobileAppConfig
     */
    public function setBGColorNavBar($bGColorNavBar)
    {
        $this->BGColorNavBar = $bGColorNavBar;
    
        return $this;
    }

    /**
     * Get BGColorNavBar
     *
     * @return string 
     */
    public function getBGColorNavBar()
    {
        return $this->BGColorNavBar;
    }

    /**
     * Set BGColorfooter
     *
     * @param string $bGColorfooter
     * @return MobileAppConfig
     */
    public function setBGColorfooter($bGColorfooter)
    {
        $this->BGColorfooter = $bGColorfooter;
    
        return $this;
    }

    /**
     * Get BGColorfooter
     *
     * @return string 
     */
    public function getBGColorfooter()
    {
        return $this->BGColorfooter;
    }

    /**
     * Set ColorContentTitle
     *
     * @param string $colorContentTitle
     * @return MobileAppConfig
     */
    public function setColorContentTitle($colorContentTitle)
    {
        $this->ColorContentTitle = $colorContentTitle;
    
        return $this;
    }

    /**
     * Get ColorContentTitle
     *
     * @return string 
     */
    public function getColorContentTitle()
    {
        return $this->ColorContentTitle;
    }

    /**
     * Set ColorHeaderTitle
     *
     * @param string $colorHeaderTitle
     * @return MobileAppConfig
     */
    public function setColorHeaderTitle($colorHeaderTitle)
    {
        $this->ColorHeaderTitle = $colorHeaderTitle;
    
        return $this;
    }

    /**
     * Get ColorHeaderTitle
     *
     * @return string 
     */
    public function getColorHeaderTitle()
    {
        return $this->ColorHeaderTitle;
    }

    /**
     * Set ColorNavBarTitle
     *
     * @param string $colorNavBarTitle
     * @return MobileAppConfig
     */
    public function setColorNavBarTitle($colorNavBarTitle)
    {
        $this->ColorNavBarTitle = $colorNavBarTitle;
    
        return $this;
    }

    /**
     * Get ColorNavBarTitle
     *
     * @return string 
     */
    public function getColorNavBarTitle()
    {
        return $this->ColorNavBarTitle;
    }

    /**
     * Set IsPublished
     *
     * @param boolean $isPublished
     * @return MobileAppConfig
     */
    public function setIsPublished($isPublished)
    {
        $this->IsPublished = $isPublished;
    
        return $this;
    }

    /**
     * Get IsPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->IsPublished;
    }

    /**
     * Set Conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\wwwConf $conference
     * @return MobileAppConfig
     */
    public function setConference(\fibe\Bundle\WWWConfBundle\Entity\wwwConf $conference = null)
    {
        $this->Conference = $conference;
    
        return $this;
    }

    /**
     * Get Conference
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\wwwConf 
     */
    public function getConference()
    {
        return $this->Conference;
    }



}