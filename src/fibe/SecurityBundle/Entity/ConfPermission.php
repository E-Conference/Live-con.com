<?php

  namespace fibe\SecurityBundle\Entity;

  use FOS\UserBundle\Entity\User as BaseUser;
  use Doctrine\ORM\Mapping as ORM;

  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;

  /**
   * plain old object used as dto for user permissions about the current conference
   */
  class ConfPermission
  { 

    protected $action;

    protected $repositoryName;

    protected $entityId; 

    protected $entityLabel;

    protected $restricted;

    public function setAction($action)
    {
      $this->action = $action;

      return $this;
    }

    public function getAction()
    {
      return $this->action;
    }


    public function setRepositoryName($repositoryName)
    {
      $this->repositoryName = $repositoryName;

      return $this;
    }

    public function getRepositoryName()
    {
      return $this->repositoryName;
    }


    public function setEntityId($entityId)
    {
      $this->entityId = $entityId;

      return $this;
    }

    public function getEntityId()
    {
      return $this->entityId;
    }


    public function setEntityLabel($entityLabel)
    {
      $this->entityLabel = $entityLabel;

      return $this;
    }

    public function getEntityLabel()
    {
      return $this->entityLabel;
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


  }