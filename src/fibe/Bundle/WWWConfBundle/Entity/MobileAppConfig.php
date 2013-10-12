<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity define a keyword
 *
 *  @ORM\Table(name="mobileAppConfig")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\MobileAppConfigRepository")
 *  @ORM\HasLifecycleCallbacks
 */
class MobileAppConfig
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   /**
     * BGContent
     *
     *
     * @ORM\Column(type="string", name="BGContent", nullable=true)
     */
    protected $BGColorContent;

     /**
     * BGHeader
     *
     *
     * @ORM\Column(type="string", name="BGHeader", nullable=true)
     */
    protected $BGColorHeader;

     /**
     * BGNavBar
     *
     *
     * @ORM\Column(type="string", name="BGNavBar", nullable=true)
     */
    protected $BGColorNavBar;


     /**
     * BGfooter
     *
     *
     * @ORM\Column(type="string", name="BGfooter", nullable=true)
     */
    protected $BGColorfooter;


     /**
     * ColorContentTitle
     *
     *
     * @ORM\Column(type="string", name="ColorContentTitle", nullable=true)
     */
    protected $ColorContentTitle;

      /**
     * ColorHeaderTitle
     *
     *
     * @ORM\Column(type="string", name="ColorHeaderTitle", nullable=true)
     */
    protected $ColorHeaderTitle;


      /**
     * ColorNavBarTitle
     *
     *
     * @ORM\Column(type="string", name="ColorNavBarTitle", nullable=true)
     */
    protected $ColorNavBarTitle;


     /**
     * IsPublished
     *
     *
     * @ORM\Column(type="boolean", name="IsPublished", nullable=true)
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


}