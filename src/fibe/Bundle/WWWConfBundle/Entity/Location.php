<?php

/**
 *
 * @author :  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use fibe\Bundle\WWWConfBundle\Entity\Equipment;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\LocationRepository")
 */
class Location
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string", length=128)
   */
  protected $name;

  /**
   * @ORM\Column(type="integer", nullable=true)
   */
  protected $capacity;

  /**
   *
   * @ORM\ManyToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Equipment")
   */
  protected $equipments;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  protected $description;

  /**
   * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
   *
   * @Assert\Length(
   *      min = "-90",
   *      max = "90",
   *      minMessage = "You must be between -90 and 90.",
   *      maxMessage = "YYou must be between -90 and 90."
   * )
   */
  protected $latitude;

  /**
   * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
   * @Assert\Length(
   *      min = "-180",
   *      max = "180",
   *      minMessage = "You must be between -180 and 180.",
   *      maxMessage = "YYou must be between -180 and 180."
   * )
   */
  protected $longitude;

  /**
   * @ORM\OneToMany(targetEntity="CalendarEntity", mappedBy="location")
   */
  protected $calendarEntities;

  /**
   * conference
   *
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="locations", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   */
  private $conference;

  /**
   * Constructor
   */
  public function __construct()
  {
  }

  /**
   * toString
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getName();
  }

  /**
   * getGeo
   *
   * @return string
   */
  public function getGeo()
  {
    return sprintf('%.6f;%.6f', $this->getLatitude(), $this->getLongitude());
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
   * Set name
   *
   * @param string $name
   *
   * @return Location
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

  /** Set capacity
   *
   * @param string $capacity
   *
   * @return Location
   */
  public function setCapacity($capacity)
  {
    $this->capacity = $capacity;

    return $this;
  }

  /**
   * Get capacity
   *
   * @return integer
   */
  public function getCapacity()
  {
    return $this->capacity;
  }

  /**
   * Set description
   *
   * @param string $description
   *
   * @return Location
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set latitude
   *
   * @param float $latitude
   *
   * @return Location
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;

    return $this;
  }

  /**
   * Get latitude
   *
   * @return float
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * Set longitude
   *
   * @param float $longitude
   *
   * @return Location
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;

    return $this;
  }

  /**
   * Get longitude
   *
   * @return float
   */
  public function getLongitude()
  {
    return $this->longitude;
  }

  /**
   * Add locationAwareCalendarEntities
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\LocationAwareCalendarEntity $locationAwareCalendarEntities
   *
   * @return Location
   */
  public function addLocationAwareCalendarEntitie(\fibe\Bundle\WWWConfBundle\Entity\LocationAwareCalendarEntity $locationAwareCalendarEntities)
  {
    $this->locationAwareCalendarEntities[] = $locationAwareCalendarEntities;

    return $this;
  }

  /**
   * Remove locationAwareCalendarEntities
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\LocationAwareCalendarEntity $locationAwareCalendarEntities
   */
  public function removeLocationAwareCalendarEntitie(\fibe\Bundle\WWWConfBundle\Entity\LocationAwareCalendarEntity $locationAwareCalendarEntities)
  {
    $this->locationAwareCalendarEntities->removeElement($locationAwareCalendarEntities);
  }

  /**
   * Get locationAwareCalendarEntities
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getLocationAwareCalendarEntities()
  {
    return $this->locationAwareCalendarEntities;
  }

  /**
   * Add Equipment
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Equipment $equipments
   *
   * @return ConfEvent
   */
  public function addEquipment(\fibe\Bundle\WWWConfBundle\Entity\Equipment $equipments)
  {
    $this->equipments[] = $equipments;

    return $this;
  }

  /**
   * Remove Equipment
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Equipment $equipments
   */
  public function removeEquipment(\fibe\Bundle\WWWConfBundle\Entity\Equipment $equipments)
  {
    $this->equipments->removeElement($equipments);
  }

  /**
   * Get Equipments
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEquipments()
  {
    return $this->equipments;
  }

  /**
   * Set conference
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference
   *
   * @return ConfEvent
   */
  public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference)
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
