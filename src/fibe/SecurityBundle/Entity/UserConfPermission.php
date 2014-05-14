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

  }