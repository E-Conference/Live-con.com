<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\wwwConf;
use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;
use fibe\Bundle\WWWConfBundle\Util\StringTools;

/**
 * This entity define a paper of a conference
 *
 *
 * @ORM\Table(name="paper")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\PaperRepository")
 * @ORM\HasLifecycleCallbacks
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
   * @ORM\ManyToMany(targetEntity="Person", inversedBy="papers", cascade={"persist", "merge"})
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
   * topic(topics)
   *
   * @ORM\ManyToMany(targetEntity="Topic", inversedBy="papers", cascade={"persist"})
   * @ORM\JoinTable(name="paper_topic",
   *     joinColumns={@ORM\JoinColumn(name="paper_id", referencedColumnName="id", onDelete="Cascade")},
   *     inverseJoinColumns={@ORM\JoinColumn(name="topic_id", referencedColumnName="id", onDelete="Cascade")})
   */
  protected $topics;

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
   * @ORM\Column(type="string", length=256, nullable=true)
   */
  protected $slug;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->authors = new \Doctrine\Common\Collections\ArrayCollection();
    $this->topic = new \Doctrine\Common\Collections\ArrayCollection();
    $this->events = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * __toString method
   *
   * @return mixed
   */
  public function __toString()
  {
    return $this->title;
  }

  /**
   * Slugify
   * @ORM\PrePersist()
   */
  public function slugify()
  {
    $this->setSlug(StringTools::slugify($this->getId() . $this->getTitle()));
  }

  /**
   * onUpdate
   *
   * @ORM\PostPersist()
   * @ORM\PreUpdate()
   */
  public function onUpdate()
  {
    $this->slugify();
  }

  /**
   * Set slug
   *
   * @param string $slug
   *
   * @return ConfEvent
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Get slug
   *
   * @return string
   */
  public function getSlug()
  {
    $this->slugify();

    return $this->slug;
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
   *
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
   *
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
   *
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
   *
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
   *
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
   *
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
   * Add topics
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
   *
   * @return Paper
   */
  public function addTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
  {
    $this->topics[] = $topics;

    return $this;
  }

  /**
   * Remove topics
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
   */
  public function removeTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
  {
    $this->topics->removeElement($topics);
  }

  /**
   * Get topics
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTopics()
  {
    return $this->topics;
  }

  /**
   * Add events
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
   *
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
   *
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
}
