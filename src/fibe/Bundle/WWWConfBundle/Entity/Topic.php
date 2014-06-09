<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Util\StringTools;


/**
 * This entity define a topic
 *
 * @ORM\Table(name="topic")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\TopicRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Topic
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * name
   *
   * @ORM\Column(type="string", name="name")
   */
  protected $name;

  /**
   * Papers related to thise topic
   *
   * @ORM\ManyToMany(targetEntity="Paper", mappedBy="topics", cascade={"persist"})
   */
  private $papers;

  /**
   * events related to this topic
   *
   * @ORM\ManyToMany(targetEntity="ConfEvent", mappedBy="topics", cascade={"persist"})
   */
  private $events;

  /**
   *  Topics associated to this conference
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="topics", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
  protected $conference;

  /**
   * @ORM\Column(type="string", length=128, nullable=true)
   */
  protected $slug;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->papers = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * __toString method
   *
   * @return mixed
   */
  public function __toString()
  {
    return $this->name;

  }

  /**
   * Slugify
   *
   */
  public function slugify()
  {
    $this->setSlug(StringTools::slugify($this->getId() . $this->getName()));
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
   * Add papers
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
   *
   * @return Topic
   */
  public function addPaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
  {
    $this->papers[] = $papers;

    return $this;
  }

  /**
   * Remove papers
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
   */
  public function removePaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
  {
    $this->papers->removeElement($papers);
  }

  /**
   * Get papers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPapers()
  {
    return $this->papers;
  }

  /**
   * Add events
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
   *
   * @return Topic
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
   * Set name
   *
   * @param string $name
   *
   * @return Topic
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
   * @return Topic
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
