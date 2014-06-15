<?php

namespace fibe\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity define a team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Team
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
   * Conference
   *
   * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf",cascade={"persist","remove"})
   * @ORM\JoinColumn(name="conference", referencedColumnName="id",onDelete="CASCADE")
   */
  private $conference;


  /**
   * confManager
   *
   * @ORM\ManyToMany(targetEntity="fibe\SecurityBundle\Entity\User", mappedBy="teams",cascade={"persist"})
   */
  private $confManagers;

  public function __construct()
  {
    $this->confManagers = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }


  public function setConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conference = null)
  {
    $this->conference = $conference;

    return $this;
  }

  public function getConference()
  {
    return $this->conference;
  }


  /**
   * Add a conference manager
   *
   * @param User $confManager
   *
   * @return $this
   */
  public function addConfManager(\fibe\SecurityBundle\Entity\User $confManager = null)
  {
    $this->confManagers[] = $confManager;

    return $this;
  }

  /**
   * Remove a conference manager
   *
   * @param User $confManager
   */
  public function removeConfManager(\fibe\SecurityBundle\Entity\User $confManager)
  {
    $this->confManagers->removeElement($confManager);
  }


  /**
   * Return all conference managers
   *
   * @return \Doctrine\Common\Collections\ArrayCollection
   */
  public function getConfManagers()
  {
    return $this->confManagers;
  }
}