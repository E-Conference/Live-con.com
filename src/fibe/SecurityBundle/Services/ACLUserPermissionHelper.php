<?php
  namespace fibe\SecurityBundle\Services;

  use Symfony\Component\Security\Core\SecurityContext; 
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\QueryBuilder;
  use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
  use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
  use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
  use Symfony\Component\Security\Acl\Permission\MaskBuilder;
  use fibe\SecurityBundle\Services\ACLHelper;
  use fibe\SecurityBundle\Services\ACLEntityHelper;
  use fibe\SecurityBundle\Entity\UserConfPermission;
  use fibe\SecurityBundle\Entity\ConfPermission;
  use fibe\SecurityBundle\Services\AceNotFoundException;

  use fibe\SecurityBundle\Form\UserConfPermissionType;

  /** 
   * EXPLICATION DE LA TABLE DES ROLES :
   *     http://symfony.com/fr/doc/current/cookbook/security/acl_advanced.html#table-de-permission-integree
   */
  class ACLUserPermissionHelper extends ACLEntityHelper
  {

    /** @const */
    public static $belongsToConfRepositories = array(
      'ConfEvent',
      'Location',
      'Paper',
      'Person',
      'Role',
      'Organization',
      'Topic'
    ); 

    const NOT_AUTHORYZED_UPDATE_RIGHT_LABEL = 'You need to be MASTER to be able to change other user permission on %s %s ';
 

    //get or create a default UserConfPermission object for the given user or the current user if manager=null

    public function getUserConfPermission($manager=null, $restrict = true)
    {
      $currentConf = $this->getCurrentConf();

      $userConfPermission = new UserConfPermission();
      if($manager)$userConfPermission->setUser($manager);
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      //TODO : ajouter un champ est autorise à modif si la personne est master + changement vue
      $noManager = $manager == null;
      $user = $this->getUser();
      if($noManager) $manager = $user;


      $entity = $currentConf;
      $action = $this->getACEByEntity($entity,$user); 
      $restricted = !$restrict || ($action == "OWNER" || $action == "MASTER");
      $action = $noManager && !$restricted ? 'EDIT' : $restrict ? "VIEW" : $this->getACEByEntity($entity,$manager);

      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Conference');
      $confPermission->setAction($action);
      $confPermission->setRestricted($restricted);
      $confPermission->setRepositoryName('WwwConf');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);


      $entity = $currentConf->getAppConfig();
      $action = $this->getACEByEntity($entity,$user); 
      $restricted = !$restrict || ($action == "OWNER" || $action == "MASTER");
      $action = $noManager && !$restricted ? 'EDIT' : $restrict ? "VIEW" : $this->getACEByEntity($entity,$manager);

      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Mobile application');
      $confPermission->setAction($action);
      $confPermission->setRestricted($restricted);
      $confPermission->setRepositoryName('MobileAppConfig');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);


      $entity = $currentConf->getTeam();
      $action = $this->getACEByEntity($entity,$user); 
      $restricted = !$restrict || ($action == "OWNER" || $action == "MASTER");
      $action = $noManager && !$restricted ? 'VIEW' : $restrict ? "VIEW" : $this->getACEByEntity($entity,$manager);

      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Team');
      $confPermission->setAction($action);
      $confPermission->setRestricted($restricted);
      $confPermission->setRepositoryName('Team');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);
 
      return $userConfPermission; 
    } 
 
    public function updateUserConfPermission(UserConfPermission $userConfPermission)
    {
      // cannot demote own permission
      if($userConfPermission->getUser()->getId() == $this->getUser()->getId())
      {
        throw new AccessDeniedException("You cannot demote yourself.");
      }
      // cannot demote the owner
      try {
        if("OWNER" == $this->getACEByEntity($this->getCurrentConf(),$userConfPermission->getUser()))
        {
          throw new AccessDeniedException("You cannot demote the owner.");
        }
      } catch (AceNotFoundException $e) {
        //ignore exception : new teamate without ace
      }
      foreach ($userConfPermission->getConfPermissions() as $confPermission)
      {
        $repositoryName = $confPermission->getRepositoryName();
        $action = $confPermission->getAction();
        $id=$confPermission->getEntityId();

        //if it's the conference object, update all object in static::$belongsToConfRepositories
        if($repositoryName == ACLEntityHelper::LINK_WITH)
        {
          foreach (static::$belongsToConfRepositories as $subRepositoryName) {
            $this->updateUserACL($userConfPermission->getUser(), $action, $subRepositoryName);
          }
        }
        $this->updateUserACL($userConfPermission->getUser(), $action, $repositoryName, $id);
      }
    }

    /**
     * ONLY OPERATOR ON TEAM CAN DO THAT
     * @param  [type] $teamateId      the choosen teamate id
     * @param  [type] $action         [description]
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             if not set, update all objects of the repository given linked with the current conf
     */
    private function updateUserACL($teamateId,$action,$repositoryName,$id=null)
    { 
      if(!$id)
      {
        $entities = $this->getEntitiesACL("VIEW",$repositoryName); 
        foreach ($entities as $entity)
        {
          $this->performUpdateUserACL($teamateId,$action,$entity);
        }        
      } else
      {
        $entity = $this->getEntityACL("VIEW",$repositoryName,$id);  
        $this->performUpdateUserACL($teamateId,$action,$entity);
      } 
    }

    //si l'utilisateur est master = OK 
    //  sinon s'il est operator et qu'il veux ajouter un membre  ( catch (AclNotFoundException $e) )
    //          => affecter "view" comme droit par defaut, 
    //  sinon refuser
    private function performUpdateUserACL($teamateId,$action,$entity)
    {
      $user = $this->getUser($teamateId); 
      $userSecurityIdentity = UserSecurityIdentity::fromAccount($user); 
      $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
      $acl = $this->getOrCreateAcl($entitySecurityIdentity,$userSecurityIdentity);
      $this->updateOrCreateAcl($entity,$this->getUser($teamateId),$acl,$action,$userSecurityIdentity);
 
      $this->aclProvider->updateAcl($acl);
    }

    private function getOrCreateAcl($entitySecurityIdentity,$userSecurityIdentity)
    {
      $acl;
      try
      { 
        $acl = $this->aclProvider->findAcl(
          $entitySecurityIdentity,
          array($userSecurityIdentity)
        );
      }
      catch (AclNotFoundException $e)
      {
        $acl = $this->aclProvider->createAcl($entitySecurityIdentity);
      }
      return $acl;
    }

    private function updateOrCreateAcl($entity,$user,$acl,$action,$userSecurityIdentity)
    {
      try {
          $index = $this->getACEByEntity($entity,$user,"index",$acl); 

          //master permission required to update permissions
          if("OPERATOR" != $action && "CREATE" != $action )
          {
            $acl->updateObjectAce(
              $index,
              $this->getMask($action)
            );
          }
      }
      catch (AceNotFoundException $e)
      {
        //if not master : set default right to view
          if("OPERATOR" == $action || "CREATE" == $action )
          {
            $acl->insertObjectAce(
              $userSecurityIdentity,
              $this->getMask("VIEW")
            );
          }
          else
          {
            $acl->insertObjectAce(
              $userSecurityIdentity,
              $this->getMask($action)
            );
          } 
      }  
    }
  } 