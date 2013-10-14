<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\wwwConf;
use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;

/**
 * This entity define a paper of a conference
 *
 *
 *   @ORM\Table(name="paper")
 *  @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PaperRepository")
 *
 */

class Paper
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


     /**
     * autho
     * Persons related to an event 
     *  
     * @ORM\OneToMany(targetEntity="Author", mappedBy="paper")
     */
    private $author;

     /**
     * @ORM\ManyToMany(targetEntity="Keyword", inversedBy="papers", cascade={"persist"})
     * @ORM\JoinTable(name="subject",
     *     joinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="id", onDelete="Cascade")})
     */
    private $keywords;
	
    /**
     * type
     *
     *
     * @ORM\Column(type="string", nullable=true, name="type")
     */
    protected $type;


     /**
     * confEvents
     * Events related to an paper
     *
     * @ORM\ManyToMany(targetEntity="ConfEvent", mappedBy="papers", cascade={"persist"})
     */
    private $confEvents;

     /**
     * label
     *
     *
     * @ORM\Column(type="string", nullable=true,  name="label")
     */
    protected $label;
	
	/**
     *  Conference associated to this paper
     * @ORM\ManyToOne(targetEntity="wwwConf")
     * @ORM\JoinColumn(name="wwwConf_id", referencedColumnName="id")
     *
     */
    protected $conference;

    /**
     * title
     *
     *
     * @ORM\Column(type="string", name="title")
     */
    protected $title;


    /**
     * abstract
     *
     *
     * @ORM\Column(type="text", name="abstract")
     */
    protected $abstract;

    /**
     * month
     *
     *
     * @ORM\Column(type="string", nullable=true,   name="month")
     */
    protected $month;

     /**
     * year
     *
     *
     * @ORM\Column(type="string", nullable=true, name="year")
     */
    protected $year;

     /**
     * url_pdf
     *
     *
     * @ORM\Column(type="string", nullable=true, name="url_pdf")
     */
    protected $url_pdf;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->author = new \Doctrine\Common\Collections\ArrayCollection();
        $this->keywords = new \Doctrine\Common\Collections\ArrayCollection();
        $this->confEvents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set type
     *
     * @param string $type
     * @return Paper
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Paper
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Paper
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set abstract
     *
     * @param string $abstract
     * @return Paper
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    
        return $this;
    }

    /**
     * Get abstract
     *
     * @return string 
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set month
     *
     * @param string $month
     * @return Paper
     */
    public function setMonth($month)
    {
        $this->month = $month;
    
        return $this;
    }

    /**
     * Get month
     *
     * @return string 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return Paper
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set url_pdf
     *
     * @param string $urlPdf
     * @return Paper
     */
    public function setUrlPdf($urlPdf)
    {
        $this->url_pdf = $urlPdf;
    
        return $this;
    }

    /**
     * Get url_pdf
     *
     * @return string 
     */
    public function getUrlPdf()
    {
        return $this->url_pdf;
    }

    /**
     * Add author
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Author $author
     * @return Paper
     */
    public function addAuthor(\fibe\Bundle\WWWConfBundle\Entity\Author $author)
    {
        $this->author[] = $author;
    
        return $this;
    }

    /**
     * Remove author
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Author $author
     */
    public function removeAuthor(\fibe\Bundle\WWWConfBundle\Entity\Author $author)
    {
        $this->author->removeElement($author);
    }

    /**
     * Get author
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add keywords
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords
     * @return Paper
     */
    public function addKeyword(\fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords)
    {
        $this->keywords[] = $keywords;
    
        return $this;
    }

    /**
     * Remove keywords
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords
     */
    public function removeKeyword(\fibe\Bundle\WWWConfBundle\Entity\Keyword $keywords)
    {
        $this->keywords->removeElement($keywords);
    }

    /**
     * Get keywords
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Add confEvents
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents
     * @return Paper
     */
    public function addConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents)
    {
        $this->confEvents[] = $confEvents;
    
        return $this;
    }

    /**
     * Remove confEvents
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents
     */
    public function removeConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents)
    {
        $this->confEvents->removeElement($confEvents);
    }

    /**
     * Get confEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfEvents()
    {
        return $this->confEvents;
    }

    /**
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\wwwConf $conference
     * @return Paper
     */
    public function setConference(\fibe\Bundle\WWWConfBundle\Entity\wwwConf $conference = null)
    {
        $this->conference = $conference;
    
        return $this;
    }

    /**
     * Get conference
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\wwwConf 
     */
    public function getConference()
    {
        return $this->conference;
    }
}