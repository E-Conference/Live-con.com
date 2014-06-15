<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\RoleType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * This entity define relation between person and an event
 *
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\RoleRepository")
 *
 *  Don't seem to work with ajax form
 * @UniqueEntity(
 *     fields={"person", "event","type"},
 *     errorPath="role",
 *     message="This person has already this role at this event"
 * )
 *
 */
class Role
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *
   * @ORM\ManyToOne(targetEntity="Person", inversedBy="roles")
   * @Assert\NotBlank(message="You have to choose a Person")
   *
   */
  private $person;

  /**
   * Event
   * Persons related to an event
   * @ORM\ManyToOne(targetEntity="ConfEvent", inversedBy="roles")
   * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
   * @Assert\NotBlank(message="You have to choose an event")
   *
   */
  private $event;

  /**
   * @ORM\ManyToOne(targetEntity="RoleType", inversedBy="roles")
   * @Assert\NotBlank(message="You have to choose an type")
   */
  private $type;

  /**
   *
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="roles", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
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
   * Set person
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Person $person
   *
   * @return Role
   */
  public function setPerson(\fibe\Bundle\WWWConfBundle\Entity\Person $person = null)
  {
    $this->person = $person;

    return $this;
  }

  /**
   * Get person
   *
   * @return \fibe\Bundle\WWWConfBundle\Entity\Person
   */
  public function getPerson()
  {
    return $this->person;
  }

  /**
   * Set event
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $event
   *
   * @return Role
   */
  public function setEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $event = null)
  {
    $this->event = $event;

    return $this;
  }

  /**
   * Get event
   *
   * @return \fibe\Bundle\WWWConfBundle\Entity\ConfEvent
   */
  public function getEvent()
  {
    return $this->event;
  }

  /**
   * Set type
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\RoleType $type
   *
   * @return Role
   */
  public function setType(\fibe\Bundle\WWWConfBundle\Entity\RoleType $type = null)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get type
   *
   * @return \fibe\Bundle\WWWConfBundle\Entity\RoleType
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conf
   *
   * @return Role
   */
  public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conf)
  {
    $this->conference = $conf;

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