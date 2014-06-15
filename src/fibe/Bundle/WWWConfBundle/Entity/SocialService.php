<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


use fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount;


/**
 * Social Services available
 *
 *
 * @ORM\Table(name="socialService")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\SocialServiceRepository")
 *
 */
class SocialService
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
  private $name;

  /**
   *   accountServiceHomepage
   *   Url of the social Service
   * @ORM\Column(type="string", name="accountServiceHomepage",  nullable=true)
   */
  private $accountServiceHomepage;

  /**
   * accounts
   *
   * @ORM\OneToMany(targetEntity="SocialServiceAccount", mappedBy="socialService")
   */
  private $accounts;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
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
   * @return SocialService
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
   * Set accountServiceHomepage
   *
   * @param string $accountServiceHomepage
   *
   * @return SocialService
   */
  public function setAccountServiceHomepage($accountServiceHomepage)
  {
    $this->accountServiceHomepage = $accountServiceHomepage;

    return $this;
  }

  /**
   * Get accountServiceHomepage
   *
   * @return string
   */
  public function getAccountServiceHomepage()
  {
    return $this->accountServiceHomepage;
  }

  /**
   * Add accounts
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts
   *
   * @return SocialService
   */
  public function addAccount(\fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts)
  {
    $this->accounts[] = $accounts;

    return $this;
  }

  /**
   * Remove accounts
   *
   * @param \fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts
   */
  public function removeAccount(\fibe\Bundle\WWWConfBundle\Entity\SocialServiceAccount $accounts)
  {
    $this->accounts->removeElement($accounts);
  }

  /**
   * Get accounts
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAccounts()
  {
    return $this->accounts;
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
}