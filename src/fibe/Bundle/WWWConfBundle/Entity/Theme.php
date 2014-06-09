<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


use fibe\Bundle\WWWConfBundle\Entity\ConfEvent;


/**
 * Define the themes for confEvent
 *
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\ThemeRepository")
 *
 */
class Theme
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *   Libelle
   * @ORM\Column(type="string", name="libelle")
   */
  private $libelle;

  /**
   * confEvents
   * Events related to a theme
   *
   * @ORM\ManyToMany(targetEntity="ConfEvent", mappedBy="themes", cascade={"persist"})
   */
  private $confEvents;

  /**
   * Constructor
   */
  public function __construct()
  {
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
   * Set libelle
   *
   * @param string $libelle
   *
   * @return Theme
   */
  public function setLibelle($libelle)
  {
    $this->libelle = $libelle;

    return $this;
  }

  /**
   * Get libelle
   *
   * @return string
   */
  public function getLibelle()
  {
    return $this->libelle;
  }

  /**
   * Add confEvents
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $confEvents
   *
   * @return Theme
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
}