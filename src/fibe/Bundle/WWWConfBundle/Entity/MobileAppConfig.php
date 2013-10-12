<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MobileAppConfig
 */
class MobileAppConfig
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $BGColorContent;

    /**
     * @var string
     */
    private $BGColorHeader;

    /**
     * @var string
     */
    private $BGColorNavBar;

    /**
     * @var string
     */
    private $BGColorfooter;

    /**
     * @var string
     */
    private $ColorContentTitle;

    /**
     * @var string
     */
    private $ColorHeaderTitle;

    /**
     * @var string
     */
    private $ColorNavBarTitle;

    /**
     * @var boolean
     */
    private $IsPublished;

    /**
     * @var \fibe\Bundle\WWWConfBundle\Entity\wwwConf
     */
    private $Conference;


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
