<?php

namespace fibe\Bundle\WWWConfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

use fibe\Bundle\WWWConfBundle\Util\StringTools;


/**
 * This entity define a topic
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity(repositoryClass="fibe\Bundle\WWWConfBundle\Repository\SponsorRepository")
 * @ORM\HasLifecycleCallbacks
 *
 */
class Sponsor
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * Name of the sponsor
   *
   * @ORM\Column(type="string", name="name")
   */
  protected $name;

  /**
   * Url of the sponsor
   *
   * @ORM\Column(type="string", nullable=true, name="url")
   */
  protected $url;

  /**
   * Message to display for the sponsor
   *
   * @ORM\Column(type="string", nullable=true, name="message")
   */
  protected $description;

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
   * @ORM\Column(type="string", length=128, nullable=true)
   */
  protected $slug;

  /**
   * Sponsors associated to this conference
   * @ORM\ManyToOne(targetEntity="fibe\Bundle\WWWConfBundle\Entity\WwwConf", inversedBy="sponsors", cascade={"persist"})
   * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
   *
   */
  protected $conference;

  /**
   * Constructor
   */
  public function __construct()
  {

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

  /**
   * Slugify
   */
  public function slugify()
  {
    $this->setSlug(StringTools::slugify($this->getId() . $this->getName()));
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
   * @return Topic
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
   * get the url of the sponsor
   *
   * @return mixed
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * set the url of the sponsor
   *
   * @param mixed $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * @return mixed
   */
  public function getConference()
  {
    return $this->conference;
  }

  /**
   * @param mixed $conference
   */
  public function setConference($conference)
  {
    $this->conference = $conference;
  }

  /**
   * @return mixed
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param mixed $description
   */
  public function setDescription($message)
  {
    $this->description = $message;
  }

  /**
   * @return UploadedFile
   */
  public function getLogo()
  {
    return $this->logo;
  }

  /**
   * @param UploadedFile $logo
   */
  public function setLogo(UploadedFile $logo = null)
  {
    $this->logo = $logo;
  }

  /**
   * @return String
   */
  public function getLogoPath()
  {
    return $this->logoPath;
  }

  /**
   * @param String $logoPath
   */
  public function setLogoPath($logoPath)
  {
    $this->logoPath = $logoPath;
  }

  /**
   * Return the directory where the logo will be store
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
   * The name of the directory where the logo will be store
   *
   * @return string
   */
  protected function getUploadDir()
  {
    // get rid of the __DIR__ so it doesn't screw up
    // when displaying uploaded doc/image in the view.
    return 'uploads/sponsors/';
  }

  /**
   * Upload the logo to the server
   */
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
}
