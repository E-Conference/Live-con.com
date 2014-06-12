<?php

  namespace fibe\SecurityBundle\Entity;

  use FOS\UserBundle\Entity\User as BaseUser;
  use Doctrine\ORM\Mapping as ORM;

  use fibe\Bundle\WWWConfBundle\Entity\WwwConf;
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
     *
     * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf")
     * @ORM\JoinColumn(name="currentConf", referencedColumnName="id")
     */
    protected $currentConf; 

    /**
     * owner of those conferences
     *  
     * @ORM\ManyToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="confManagers", cascade={"persist"})
     * @ORM\JoinTable(name="manager_conference",
     *     joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")})
     */
    protected $conferences;

    /**
     *  invited in a conf
     *  
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="confManagers", cascade={"persist","remove"})
     * @ORM\JoinTable(name="manager_team",
     *     joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="Cascade")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="Cascade")})
     */
    protected $teams;
 
 
    /** @ORM\Column(name="name", type="string", length=255, nullable=true) */
    protected $name;
 
    /** @ORM\Column(name="picture", type="string", length=255, nullable=true) */
    protected $picture;

 
    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;
 
    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;


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

    public function getName()
    {
      return $this->name;
    }


    public function setName( $name )
    {
      $this->name = $name;

      return $this;
    }

    public function getPicture()
    {
      return $this->picture;
    }


    public function setPicture( $picture )
    {
      $this->picture = $picture;

      return $this;
    }

    /**
     * Get conferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoogleId()
    {
      return $this->google_id;
    }

    /**
     * Set currentConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf
     *
     * @return User
     */
    public function setGoogleId( $googleId )
    {
      $this->google_id = $googleId;

      return $this;
    }

    /**
     * Get conferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoogleAccessToken()
    {
      return $this->google_access_token;
    }

    /**
     * Set currentConf
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\WwwConf $currentConf
     *
     * @return User
     */
    public function setGoogleAccessToken( $googleAccessToken )
    {
      $this->google_access_token = $googleAccessToken;

      return $this;
    }

  }