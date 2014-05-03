<?php

  namespace fibe\Bundle\WWWConfBundle\Entity;

  use Symfony\Component\HttpFoundation\File\UploadedFile;
  use Doctrine\ORM\Mapping as ORM;
  use Symfony\Component\Validator\Constraints as Assert;

  use fibe\SecurityBundle\Entity\User;
  use fibe\SecurityBundle\Entity\Authorization;
  use fibe\Bundle\WWWConfBundle\Entity\MappingFile;
  use fibe\Bundle\WWWConfBundle\Util\StringTools;

  /**
   * WwwConf entity
   *
   * @ORM\Entity
   * @ORM\Table(name="conference")
   * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\WwwConfRepository")
   * @ORM\HasLifecycleCallbacks
   */
  class WwwConf
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
     * events
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\ConfEvent", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $events;

    /**
     * locations
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Location", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $locations;

    /**
     * Papers
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Paper", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $papers;

    /**
     * Persons
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Person", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $persons;

    /**
     * Roles
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Role", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $roles;

    /**
     * Topics
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Organization", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $organizations;

    /**
     * Topics
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Topic", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $topics;

    /**
     * confManager
     *
     * @ORM\ManyToMany(targetEntity="fibe\SecurityBundle\Entity\User", mappedBy="conferences",cascade={"persist"})
     */
    private $confManagers;

    /**
     *
     * @ORM\OneToMany(targetEntity="fibe\SecurityBundle\Entity\Authorization",  mappedBy="conference",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     */
    private $authorizations;


    /**
     * Mobile app configurations
     *
     * @ORM\OneToOne(targetEntity="fibe\MobileAppBundle\Entity\MobileAppConfig",cascade={"persist"})
     * @ORM\JoinColumn(name="appConfig", referencedColumnName="id")
     */
    private $appConfig;

    /**
     * mappingFiles
     *
     * @ORM\OneToMany(targetEntity="fibe\Bundle\WWWConfBundle\Entity\MappingFile", mappedBy="conference",cascade={"persist", "remove"})
     */
    private $mappingFiles;

    /**
     * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\Module")
     **/
    private $module;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="2M",
     * mimeTypes = {"image/jpeg", "image/png", "image/gif", "image/jpg"},
     * mimeTypesMessage = "The file must be an image")
     */
    private $logo;

    /**
     * @var String
     * @ORM\Column(name="logoPath", type="string", length=255,nullable=true)
     */
    private $logoPath;

    /**
     * @ORM\OneToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\ConfEvent", cascade="remove")
     **/
    private $mainConfEvent;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $slug;


    /**
     * @return string
     */
    public function __toString()
    {
      return ($this->mainConfEvent ? $this->mainConfEvent->getSummary() : "");

    }

    /**
     * Slugify
     *
     */
    public function slugify()
    {
      $this->setSlug(StringTools::slugify($this->getId() . $this->getConfName()));

    }

    /**
     * onUpdate
     *
     * @ORM\PostPersist()
     * @ORM\PreUpdate()
     */
    public function onUpdate()
    {
      $this->slugify();
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return ConfEvent
     */
    public function setSlug($slug)
    {
      $this->slug = $slug;

      return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
      return $this->slug;
    }


    /**
     * Return the id of the conference
     *
     * @return int
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * Return the name of the conference
     *
     * @return string
     */
    public function getConfName()
    {
      return ($this->mainConfEvent ? $this->mainConfEvent->getSummary() : "");
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


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setLogo(UploadedFile $logo = null)
    {
      $this->logo = $logo;

      return $this;
    }


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getLogo()
    {
      return $this->logo;
    }


    /**
     * Set the path of the confgerence's logo
     *
     * @param $logoPath
     *
     * @return $this
     */
    public function setLogoPath($logoPath)
    {
      $this->logoPath = $logoPath;
      return $this;
    }

    /**
     * Return the path of the confgerence's logo
     *
     * @return String
     */
    public function LogoPath()
    {
      return $this->logoPath;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->events = new \Doctrine\Common\Collections\ArrayCollection();
      $this->confManagers = new \Doctrine\Common\Collections\ArrayCollection();
      $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add locations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Location $locations
     *
     * @return WwwConf
     */
    public function addLocation(\fibe\Bundle\WWWConfBundle\Entity\Location $locations)
    {
      $this->locations[] = $locations;

      return $this;
    }

    /**
     * Remove locations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Location $locations
     */
    public function removeLocation(\fibe\Bundle\WWWConfBundle\Entity\Location $locations)
    {
      $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocations()
    {
      return $this->locations;
    }

    /**
     * Add papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     *
     * @return WwwConf
     */
    public function addPaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
    {
      $this->papers[] = $papers;

      return $this;
    }

    /**
     * Remove papers
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Paper $papers
     */
    public function removePaper(\fibe\Bundle\WWWConfBundle\Entity\Paper $papers)
    {
      $this->papers->removeElement($papers);
    }

    /**
     * Get papers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPapers()
    {
      return $this->papers;
    }

    /**
     * Add persons
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $persons
     *
     * @return WwwConf
     */
    public function addPerson(\fibe\Bundle\WWWConfBundle\Entity\Person $persons)
    {
      $this->persons[] = $persons;

      return $this;
    }

    /**
     * Remove persons
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Person $persons
     */
    public function removePerson(\fibe\Bundle\WWWConfBundle\Entity\Person $persons)
    {
      $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersons()
    {
      return $this->persons;
    }

    /**
     * Add app config
     *
     * @param \fibe\MobileAppBundle\Entity\MobileAppConfig $AppConfig
     *
     * @return WwwConf
     */
    public function setAppConfig($AppConfig)
    {
      $this->appConfig = $AppConfig;

      return $this;
    }

    public function getAppConfig()
    {
      return $this->appConfig;
    }


    /**
     * Add topics
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
     *
     * @return WwwConf
     */
    public function addTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
    {
      $this->topics[] = $topics;

      return $this;
    }

    /**
     * Remove topics
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Topic $topics
     */
    public function removeTopic(\fibe\Bundle\WWWConfBundle\Entity\Topic $topics)
    {
      $this->topics->removeElement($topics);
    }

    /**
     * Get topics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopics()
    {
      return $this->topics;
    }

    /**
     * Add organizations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
     *
     * @return WwwConf
     */
    public function addOrganization(\fibe\Bundle\WWWConfBundle\Entity\Organization $organizations)
    {
      $this->organizations[] = $organizations;

      return $this;
    }

    /**
     * Remove organizations
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\Organization $organizations
     */
    public function removeOrganization(\fibe\Bundle\WWWConfBundle\Entity\Organization $organizations)
    {
      $this->organizations->removeElement($organizations);
    }

    /**
     * Get organizations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrganizations()
    {
      return $this->organizations;
    }

    /**
     * Set mainConfEvent
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $mainConfEvent
     *
     * @return WwwConf
     */
    public function setMainConfEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $mainConfEvent = null)
    {
      $this->mainConfEvent = $mainConfEvent;

      return $this;
    }

    /**
     * Get mainConfEvent
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\ConfEvent
     */
    public function getMainConfEvent()
    {
      return $this->mainConfEvent;
    }

    /**
     * Add events
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
     *
     * @return WwwConf
     */
    public function addEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events)
    {
      $this->events[] = $events;

      return $this;
    }

    /**
     * Remove events
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events
     */
    public function removeEvent(\fibe\Bundle\WWWConfBundle\Entity\ConfEvent $events)
    {
      $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
      return $this->events;
    }

    /**
     * Get sub-events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubEvents()
    {
      $sub_events[] = $this->events;
      $sub_events->removeElement($this->mainConfEvent);
      return $sub_events;
    }


    public function uploadLogo()
    {
      // the file property can be empty if the field is not required
      if (null === $this->getLogo())
      {
        return;
      }


      // générer un nom aléatoire et essayer de deviner l'extension (plus sécurisé)
      $extension = $this->getLogo()->guessExtension();
      if (!$extension)
      {
        // l'extension n'a pas été trouvée
        $extension = 'bin';
      }
      $name = $this->getId() . '.' . $extension;
      $this->getLogo()->move($this->getUploadRootDir(), $name);
      $this->setLogoPath($name);
    }

    /**
     * @TODO comment
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
      // the absolute directory path where uploaded
      // documents should be saved
      return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }


    /**
     * @TODO comment
     *
     * @return string
     */
    protected function getUploadDir()
    {
      // get rid of the __DIR__ so it doesn't screw up
      // when displaying uploaded doc/image in the view.
      return 'uploads/';
    }

    /**
     * Get logoPath
     *
     * @return string
     */
    public function getLogoPath()
    {
      return $this->logoPath;
    }

    /**
     * Add authorizations
     *
     * @param \fibe\SecurityBundle\Entity\Authorization $authorizations
     *
     * @return WwwConf
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
     *Get an authorization for a specific user
     *
     */
    public function getAuthorizationByUser(\fibe\SecurityBundle\Entity\User $confManager)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getUser()->getId() == $confManager->getId())
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
    public function getFlagAppByManagerId($userId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getUser()->getId() == $userId)
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
    public function getFlagSchedByManagerId($userId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getUser()->getId() == $userId)
        {
          return $authorization->getFlagSched();
        }
      }
      return false;
    }

    /**
     *Get an authorization falg app for a specific conference
     *
     */
    public function getFlagDatasByManagerId($userId)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getUser()->getId() == $userId)
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
    public function getFlagByManagerId($userId, $flagType)
    {
      foreach ($this->authorizations as $authorization)
      {
        if ($authorization->getUser()->getId() == $userId)
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
     * @return bool
     */
    public function isEmpty()
    {
      return (count($this->events) <= 1)
      and (count($this->locations) == 0)
      and (count($this->papers) == 0)
      and (count($this->persons) == 0)
      and (count($this->organizations) == 0)
      and (count($this->topics) == 0);

    }

    /**
     * Add mappingFiles
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\MappingFile $mappingFiles
     *
     * @return WwwConf
     */
    public function addMappingFile(\fibe\Bundle\WWWConfBundle\Entity\MappingFile $mappingFiles)
    {
      $this->mappingFiles[] = $mappingFiles;

      return $this;
    }

    /**
     * Remove mappingFiles
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\MappingFile $mappingFiles
     */
    public function removeMappingFile(\fibe\Bundle\WWWConfBundle\Entity\MappingFile $mappingFiles)
    {
      $this->mappingFiles->removeElement($mappingFiles);
    }

    /**
     * Get mappingFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMappingFiles()
    {
      return $this->mappingFiles;
    }


    /**
     * Set module
     *
     * @param \fibe\Bundle\WWWConfBundle\Entity\ConfEvent $module
     *
     * @return WwwConf
     */
    public function setModule(\fibe\Bundle\WWWConfBundle\Entity\Module $module = null)
    {
      $this->module = $module;

      return $this;
    }

    /**
     * Get module
     *
     * @return \fibe\Bundle\WWWConfBundle\Entity\ConfEvent
     */
    public function getModule()
    {
      return $this->module;
    }
  }
