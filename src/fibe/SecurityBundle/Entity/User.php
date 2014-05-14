<?php

  namespace fibe\SecurityBundle\Entity;

  use FOS\UserBundle\Entity\User as BaseUser;
  use Doctrine\ORM\Mapping as ORM;

  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
  use fibe\SecurityBundle\Entity\Authorization;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;

  /**
   * @ORM\Entity(repositoryClass="fibe\SecurityBundle\Repository\UserRepository")
   * @ORM\Table(name="manager")
   *
   */
  class User extends BaseUser
  {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confManagers", cascade={"persist"})
     * @ORM\JoinTable(name="manager_conference",
     *     joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")})
     */
    protected $conferences;

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="confManagers", cascade={"persist"})
     * @ORM\JoinTable(name="manager_team",
     *     joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")})
     */
    protected $teams;

    /**
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf")
     * @ORM\JoinColumn(name="currentConf", referencedColumnName="id")
     */
    protected $currentConf;

    /**
     *
     * @ORM\OneToMany(targetEntity="Authorization",  mappedBy="user",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     */
    protected $authorizations;

    protected $captcha;


    /**
     * Constructor
     */
    public function __construct()
    {
      parent::__construct();
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
     * Add teams
     *
     * @param Team $teams
     *
     * @return User
     */
    public function addTeam(Team $teams)
    {
      $this->teams[] = $teams;

      return $this;
    }

    /**
     * Remove teams
     *
     * @param Team $teams
     */
    public function removeTeam(Team $teams)
    {
      $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
      return $this->teams;
    } 
    

    /**
     * Add conferences
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences
     *
     * @return User
     */
    public function addConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences)
    {
      $this->conferences[] = $conferences;

      return $this;
    }

    /**
     * Remove conferences
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences
     */
    public function removeConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conferences)
    {
      $this->conferences->removeElement($conferences);
    }

    /**
     * Get conferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConferences()
    {
      return $this->conferences;
    }

    /**
     * Set currentConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf
     *
     * @return User
     */
    public function setCurrentConf(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf = null)
    {
      $this->currentConf = $currentConf;

      return $this;
    }

    /**
     * Get currentConf
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\WwwConf
     */
    public function getCurrentConf()
    {
      return $this->currentConf;
    }


    /**
     * Add authorizations
     *
     * @param \fibe\SecurityBundle\Entity\Authorization $authorizations
     *
     * @return User
     */
    public function addAuthorization(\fibe\SecurityBundle\Entity\Authorization $authorizations)
    {
      $this->authorizations[] = $authorizations;

      return $this;
    }

    /**
     * Remove authorizations
     *
     * @param \fibe\SecurityBundle\Entity\Authorization $authorizations
     */
    public function removeAuthorization(\fibe\SecurityBundle\Entity\Authorization $authorizations)
    {
      $this->authorizations->removeElement($authorizations);
    }

    /**
     * Get authorizations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorizations()
    {
      return $this->authorizations;
    }

    /**
     *Get an authorization for a specific conference
     *
     */
    public function getAuthorizationByConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conf = null)
    {
      if ($conf == null)
      {
        throw new AccessDeniedException('you have to choose a conference !');
      }
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getConference()->getId() == $conf->getId())
        {
          return $authorization;
        }
      }
      return null;
    }

    /**
     *Get an authorization falg app for a specific conference
     *
     */
    public function getFlagAppByConferenceId($confId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getConference()->getId() == $confId)
        {
          return $authorization->getFlagApp();
        }
      }
      return false;
    }

    /**
     *Get an authorization falg app for a specific conference
     *
     */
    public function getFlagSchedByConferenceId($confId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getConference()->getId() == $confId)
        {
          return $authorization->getFlagSched();
        }
      }
      return false;
    }

    /**
     *Get an authorization flag app for a specific conference
     *
     */
    public function getFlagDatasByConferenceId($confId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getConference()->getId() == $confId)
        {
          return $authorization->getFlagconfDatas();
        }
      }
      return false;
    }

    /**
     *Get an authorization flag app for a specific conference
     *
     */
    public function getFlagByConferenceId($confId, $flagType)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getConference()->getId() == $confId)
        {
          switch ($flagType)
          {
            case 'sched':
              return $authorization->getFlagSched();
              break;
            case 'app':
              return $authorization->getFlagApp();
              break;
            case 'datas':
              return $authorization->getFlagconfDatas();
              break;
            case 'team':
              return $authorization->getFlagTeam();
              break;
          }
        }
      }
      return false;
    }


    /**
     * @TODO comment
     *
     * @param WwwConf $conf
     *
     * @return bool
     */
    public function authorizedAccesToConference(\fibe\Bundle\WWWConfBundle\Entity\WwwConf $conf)
    {
      $conferences = $this->conferences->toArray();
      if (in_array($conf, $conferences))
      {
        return true;
      }

      return false;
    }


  }