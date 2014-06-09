<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * This entity define actives module for a conference
 *
 *
 * @ORM\Table(name="module")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\ModuleRepository")
 *
 */
class Module
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * Conference
   *
   * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf",cascade={"persist","remove"})
   * @ORM\JoinColumn(name="conference", referencedColumnName="id",onDelete="CASCADE")
   */
  private $conference;

  /**
   *
   * @ORM\Column(type="boolean",options={"default" = 1})
   *
   */
  private $paperModule;

  /**
   *
   * @ORM\Column(type="boolean",options={"default" = 1})
   *
   */
  private $organizationModule;

  /**
   * Module sponsors
   *
   * @ORM\Column(type="boolean",options={"default" = 1})
   */
  private $sponsorModule;

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
   * Set conference
   *
   * @param boolean $conference
   *
   * @return MobileAppConfig
   */
  public function setConference($conference)
  {
    $this->conference = $conference;

    return $this;
  }

  /**
   * Get conference
   *
   * @return boolean
   */
  public function getConference()
  {
    return $this->conference;
  }

  /**
   * Set paperModule
   *
   * @param boolean $paperModule
   *
   * @return Module
   */
  public function setPaperModule($paperModule)
  {
    $this->paperModule = $paperModule;

    return $this;
  }

  /**
   * Get paperModule
   *
   * @return boolean
   */
  public function getPaperModule()
  {
    return $this->paperModule;
  }

  /**
   * Set organizationModule
   *
   * @param boolean $organizationModule
   *
   * @return Module
   */
  public function setOrganizationModule($organizationModule)
  {
    $this->organizationModule = $organizationModule;

    return $this;
  }

  /**
   * Get organizationModule
   *
   * @return boolean
   */
  public function getOrganizationModule()
  {
    return $this->organizationModule;
  }

  /**
   * Get sponsor module
   *
   * @return mixed
   */
  public function getSponsorModule()
  {
    return $this->sponsorModule;
  }

  /**
   * Set Sponsor module
   *
   * @param mixed $sponsorModule
   */
  public function setSponsorModule($sponsorModule)
  {
    $this->sponsorModule = $sponsorModule;
  }
}