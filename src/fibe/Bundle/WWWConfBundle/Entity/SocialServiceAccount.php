<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


use fibe\Bundle\WWWConfBundle\Entity\SocialService;
use fibe\Bundle\WWWConfBundle\Entity\Person;
use fibe\Bundle\WWWConfBundle\Entity\WwwConf;


/**
 * @TODO comment
 *
 * @ORM\Table(name="socialServiceAccount")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\SocialServiceAccountRepository")
 *
 */
class SocialServiceAccount
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   *   Name
   * @ORM\Column(type="string", name="name")
   */
  private $accountName;

  /**
   * @ORM\ManyToOne(targetEntity="Person", inversedBy="accounts")
   *
   */
  private $owner;

  /**
   *
   * @ORM\ManyToOne(targetEntity="SocialService", inversedBy="accounts")
   * @ORM\JoinColumn(name="socialService_id", referencedColumnName="id")
   *
   */
  protected $socialService;

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
   * Set accountName
   *
   * @param string $accountName
   *
   * @return SocialServiceAccount
   */
  public function setAccountName($accountName)
  {
    $this->accountName = $accountName;

    return $this;
  }

  /**
   * Get accountName
   *
   * @return string
   */
  public function getAccountName()
  {
    return $this->accountName;
  }

  /**
   * Set owner
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\Person $owner
   *
   * @return SocialServiceAccount
   */
  public function setOwner(\fibe\Bundle\WWWConfBundle\Entity\Person $owner = null)
  {
    $this->owner = $owner;

    return $this;
  }

  /**
   * Get owner
   *
   * @return \fibe\Bundle\WWWConfBundle\Entity\Person
   */
  public function getOwner()
  {
    return $this->owner;
  }

  /**
   * Set socialService
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\SocialService $socialService
   *
   * @return SocialServiceAccount
   */
  public function setSocialService(\fibe\Bundle\WWWConfBundle\Entity\SocialService $socialService = null)
  {
    $this->socialService = $socialService;

    return $this;
  }

  /**
   * Get socialService
   *
   * @return \fibe\Bundle\WWWConfBundle\Entity\SocialService
   */
  public function getSocialService()
  {
    return $this->socialService;
  }
}