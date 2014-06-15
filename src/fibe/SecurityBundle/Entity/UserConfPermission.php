<?php

namespace fibe\SecurityBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

/**
 * plain old object used as dto for user permissions about the current conference
 */
class UserConfPermission
{

  protected $user;

  protected $confPermissions;

  /**
   * if the user can edit the form ( need to have at least one master permission on a permission in the confPermission array)
   * @var [type]
   */
  protected $restricted;

  /**
   * if the user can edit the form ( need to have at least one master permission on a permission in the confPermission array)
   * @var [type]
   */
  protected $isOwner;


  public function __construct()
  {
    $this->confPermissions = new ArrayCollection();
  }

  public function setUser(User $user)
  {
    $this->user = $user;

    return $this;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function addConfPermission(ConfPermission $confPermission)
  {
    $this->confPermissions[] = $confPermission;
  }

  public function getConfPermissions()
  {
    return $this->confPermissions;
  }

  public function setConfPermissions(ArrayCollection $confPermissions)
  {
    $this->confPermissions = $confPermissions;

    return $this;
  }

  public function setRestricted($restricted)
  {
    $this->restricted = $restricted;

    return $this;
  }

  public function getRestricted()
  {
    return $this->restricted;
  }

  public function setIsOwner($isOwner)
  {
    $this->isOwner = $isOwner;

    return $this;
  }

  public function getIsOwner()
  {
    return $this->isOwner;
  }

}