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
     * TitleColorContent
     *
     *
     * @ORM\Column(type="string", name="TitleColorContent", nullable=true)
     */
    protected $TitleColorContent;

    /**
     * TitleColorHeader
     *
     *
     * @ORM\Column(type="string", name="TitleColorHeader", nullable=true)
     */
    protected $TitleColorHeader;


      /**
     * TitleColorNavBar
     *
     *
     * @ORM\Column(type="string", name="TitleColorNavBar", nullable=true)
     */
    protected $TitleColorNavBar;


    /**
     * TitleColorContent
     *
     *
     * @ORM\Column(type="string", name="TitleColorFooter", nullable=true)
     */
    protected $TitleColorFooter;



    /**
     * BGColorButton
     *
     *
     * @ORM\Column(type="string", name="BGColorButton", nullable=true)
     */
    protected $BGColorButton;

    /**
     * TitleColorButton
     *
     *
     * @ORM\Column(type="string", name="TitleColorButton", nullable=true)
     */
    protected $TitleColorButton;

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
     * Set TitleColorContent
     *
     * @param string $TitleColorContent
     * @return MobileAppConfig
     */
    public function setTitleColorContent($TitleColorContent)
    {
        $this->TitleColorContent = $TitleColorContent;
    
        return $this;
    }

    /**
     * Get TitleColorContent
     *
     * @return string 
     */
    public function getTitleColorContent()
    {
        return $this->TitleColorContent;
    }

    /**
     * Set TitleColorHeader
     *
     * @param string $TitleColorHeader
     * @return MobileAppConfig
     */
    public function setTitleColorHeader($TitleColorHeader)
    {
        $this->TitleColorHeader = $TitleColorHeader;
    
        return $this;
    }

    /**
     * Get TitleColorHeader
     *
     * @return string 
     */
    public function getTitleColorHeader()
    {
        return $this->TitleColorHeader;
    }

    /**
     * Set TitleColorNavBar
     *
     * @param string $TitleColorNavBar
     * @return MobileAppConfig
     */
    public function setTitleColorNavBar($TitleColorNavBar)
    {
        $this->TitleColorNavBar = $TitleColorNavBar;
    
        return $this;
    }

    /**
     * Get TitleColorNavBar
     *
     * @return string 
     */
    public function getTitleColorNavBar()
    {
        return $this->TitleColorNavBar;
    }


     /**
     * Get TitleColorFooter
     *
     * @return string 
     */
    public function getTitleColorFooter()
    {
        return $this->TitleColorFooter;
    }


     /**
     * Set TitleColorFooter
     *
     * @param string $titleColorFooter
     * @return MobileAppConfig
     */
    public function setTitleColorFooter($titleColorFooter)
    {
        $this->TitleColorFooter = $titleColorFooter;
    
        return $this;
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
     * Set BGColorButton
     *
     * @param string $BGColorButton
     * @return MobileAppConfig
     */
    public function setBGColorButton($BGColorButton)
    {
        $this->BGColorButton = $BGColorButton;
    
        return $this;
    }

     /**
     * Get BGColorButtond
     *
     * @return string
     */
    public function getBGColorButton()
    {
        return $this->BGColorButton;
    }

      /**
     * Set BGColorButton
     *
     * @param string $TitleColorButton
     * @return MobileAppConfig
     */
    public function setTitleColorButton($TitleColorButton)
    {
        $this->TitleColorButton = $TitleColorButton;
    
        return $this;
    }

     /**
     * Get TitleColorButton
     *
     * @return string 
     */
    public function getTitleColorButton()
    {
        return $this->TitleColorButton;
    }

    


}