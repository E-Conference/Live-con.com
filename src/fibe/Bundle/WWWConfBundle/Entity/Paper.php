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
    private $id;

    /**
     * title
     *
     *
     * @ORM\Column(type="string", name="title")
     */
    private $title;

    /**
     * abstract
     * events in datasets may don't have abstract
     *
     * @ORM\Column(type="text", name="abstract", nullable=true)
     */
    private $abstract;

    /**
     * url
     *
     *
     * @ORM\Column(type="string", nullable=true, name="url")
     */
    private $url;

     /**
     * authors
     * Persons related to an event 
     *   
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="papers", cascade={"persist"})
     * @ORM\JoinTable(
     *     joinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="Cascade")})
     */
    protected $authors;

   
    /**
     * publisher
     *
     *
     * @ORM\Column(type="string", nullable=true, name="publisher")
     */
    private $publisher;

    /**
     * publishDate
     *
     *
     * @ORM\Column(type="string", nullable=true, name="publishDate")
     */
    private $publishDate;


    /**
     * subject(keywords) 
     *
     * @ORM\ManyToMany(targetEntity="Keyword", inversedBy="papers", cascade={"persist"})
     * @ORM\JoinTable(name="subject",
     *     joinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="id", onDelete="Cascade")})
     */
    protected $subjects;
	

     /**
     * confEvents
     * Events related to an paper
     *
     * @ORM\ManyToMany(targetEntity="ConfEvent", mappedBy="papers", cascade={"persist"})
     */
    protected $events;

   
	/**
     *  Conference associated to this paper
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="papers", cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     *
     */
    protected $conference;

  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->subject = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set url
     *
     * @param string $url
     * @return Paper
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set publisher
     *
     * @param string $publisher
     * @return Paper
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    
        return $this;
    }

    /**
     * Get publisher
     *
     * @return string 
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Set publishDate
     *
     * @param string $publishDate
     * @return Paper
     */
    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;
    
        return $this;
    }

    /**
     * Get publishDate
     *
     * @return string 
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Add authors
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $authors
     * @return Paper
     */
    public function addAuthor(\fibe\Bundle\WWWConfBundle\Entity\Person $authors)
    {
        $this->authors[] = $authors;
    
        return $this;
    }

    /**
     * Remove authors
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $authors
     */
    public function removeAuthor(\fibe\Bundle\WWWConfBundle\Entity\Person $authors)
    {
        $this->authors->removeElement($authors);
    }

    /**
     * Get authors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Add subject
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $subject
     * @return Paper
     */
    public function addSubject(\fibe\Bundle\WWWConfBundle\Entity\Keyword $subject)
    {
        $this->subjects[] = $subject;
    
        return $this;
    }

    /**
     * Remove subject
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Keyword $subject
     */
    public function removeSubject(\fibe\Bundle\WWWConfBundle\Entity\Keyword $subject)
    {
        $this->subjects->removeElement($subject);
    }

    /**
     * Get subject
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Add events
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
     * @return Paper
     */
    public function addEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events)
    {
        $this->events[] = $events;
    
        return $this;
    }

    /**
     * Remove events
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
     */
    public function removeEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set conference
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
     * @return Paper
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

    public function __toString(){
        return $this->title;
    }
}