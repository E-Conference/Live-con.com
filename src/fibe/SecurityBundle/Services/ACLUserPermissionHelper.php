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
  
    /**
     * get or create a UserConfPermission object for the given user or the current user if manager=null
     * @param  [User]  $manager       the manager to get permissions of. if null : current user
     * @param  boolean $restrict      if none : returns "view" as action  if no manager given to add teamate when the given user doesn't have the owner required permission to update others permission  on the object   
     * @return [UserConfPermission]   object listing permission for an user used to show or build a form
     */
    public function getUserConfPermission($manager=null, $restrictForm = true)
    {
      $currentConf = $this->getCurrentConf();
      $user = $this->getUser();

      $userConfPermission = new UserConfPermission();
      $formAllowed = false;
      if($manager)$userConfPermission->setUser($manager);
      $noManager = ($manager == null);
      if($noManager) $manager = $user;


      $entity = $currentConf;
      $currentUserAction = $this->getACEByEntity($entity,$user); 
      $isMaster = ($currentUserAction == "OWNER" || $currentUserAction == "MASTER");
      $allowed = !$restrictForm || $isMaster; 
      $action = !$allowed ? 'VIEW' : ($noManager ? 'EDIT' : $this->getACEByEntity($entity,$manager));
      $formAllowed |= $isMaster;
      
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Conference');
      $confPermission->setAction($action);
      $confPermission->setRestricted(!$allowed);
      $confPermission->setRepositoryName('WwwConf');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);


      $entity = $currentConf->getAppConfig();
      $currentUserAction = $this->getACEByEntity($entity,$user); 
      $isMaster = ($currentUserAction == "OWNER" || $currentUserAction == "MASTER"); 
      $allowed = !$restrictForm || $isMaster;
      $action = !$allowed ? 'VIEW' : ($noManager ? 'EDIT' : $this->getACEByEntity($entity,$manager));
      $formAllowed |= $isMaster;
      
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Mobile application');
      $confPermission->setAction($action);
      $confPermission->setRestricted(!$allowed);
      $confPermission->setRepositoryName('MobileAppConfig');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);


      $entity = $currentConf->getTeam();
      $currentUserAction = $this->getACEByEntity($entity,$user); 
      $isMaster = ($currentUserAction == "OWNER" || $currentUserAction == "MASTER");
      $allowed = !$restrictForm || $isMaster;
      $action = !$allowed ? 'VIEW' : ($noManager ? 'VIEW' : $this->getACEByEntity($entity,$manager));
      $formAllowed |= $isMaster;
      
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Team');
      $confPermission->setAction($action);
      $confPermission->setRestricted(!$allowed);
      $confPermission->setRepositoryName('Team');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);
      
      $userConfPermission->setRestricted(!$formAllowed);
      
      return $userConfPermission; 
    } 
 
    /**
     * process UserConfPermission to change permissions of UserConfPermission->getUser()
     * @param  UserConfPermission $userConfPermission the object with the user & his permission
     */
    public function updateUserConfPermission(UserConfPermission $userConfPermission)
    {
      $teamate = $userConfPermission->getUser();
      // cannot demote own permission
      if($teamate->getId() == $this->getUser()->getId())
      {
        throw new AccessDeniedException("You cannot demote yourself.");
      }
      // cannot demote the owner of the conference
      try {
        if("OWNER" == $this->getACEByEntity($this->getCurrentConf(),$teamate))
        {
          throw new AccessDeniedException("You cannot demote the owner.");
        }
      } catch (AceNotFoundException $e) {
        //ignore AceNotFoundException : new teamate without ace cannot be found...
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
            $this->updateUserACL($teamate, $action, $subRepositoryName);
          }
        }
        $this->updateUserACL($teamate, $action, $repositoryName, $id);
      }
    }

    public function createUserACL($teamate,$entity)
    {
      try {
        $action = $this->getACEByEntity($entity,$teamate);
      } catch (AceNotFoundException $e) {
        $action = $this->getACEByEntity($this->getCurrentConf(),$teamate);
      }
      $this->performUpdateUserACL($teamate,$action,$entity);
    }

    /**
     * update user acl by entity 
     *   /!\ doesn't check owner demoting and own permission change
     *   /!\ see updateUserConfPermission for those requirment check
     * @param  [type] $teamate [description]
     * @param  [type] $action  [description]
     * @param  [type] $entity  [description] 
     */
    private function performUpdateUserACL($teamate,$action,$entity)
    { 
      $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
      $acl = $this->getOrCreateAcl($entitySecurityIdentity,$teamate);
      $this->updateOrCreateAce($acl,$entity,$teamate,$action);
 
      $this->aclProvider->updateAcl($acl);
    }

    /** 
     * update the teamate right by repository & id
     * @param  [type] $teamate        the choosen teamate id
     * @param  [type] $action         [description]
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             if not set, update all objects of the repository given linked with the current conf
     */
    private function updateUserACL($teamate,$action,$repositoryName,$id=null)
    { 
      if(!$id)
      {
        $entities = $this->getEntitiesACL("VIEW",$repositoryName); 
        foreach ($entities as $entity)
        {
          $this->performUpdateUserACL($teamate,$action,$entity);
        }        
      } else
      {
        $entity = $this->getEntityACL("VIEW",$repositoryName,$id);  
        $this->performUpdateUserACL($teamate,$action,$entity);
      } 
    }

    private function getOrCreateAcl($entitySecurityIdentity,$user)
    {
      $userSecurityIdentity = UserSecurityIdentity::fromAccount($user);
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

    /**
     * process permission change
     *
     *  if the user is master : OK
     *  else if he's OPERATOR and he wants to add a member ( catch (AclNotFoundException $e) )
     *        affect VIEW as default right
     *  else : do nothing
     * @param  [type] $entity               the object ot change permission
     * @param  [type] $user                 the user to change permission
     * @param  [type] $acl                  the acl to update
     * @param  [type] $action               the action to set
     */
    private function updateOrCreateAce($acl,$entity,$user,$action)
    {
      $currentUserRight = $this->getACEByEntity($entity); 
      try {
          //get the ace index
          $index = $this->getACEByEntity($entity,$user,"index",$acl); 

          //master permission required to update permissions
          if("MASTER" == $currentUserRight || "OWNER" == $currentUserRight )
          {
            $acl->updateObjectAce(
              $index,
              $this->getMask($action)
            );
          }else{ 
            throw new \Exception("   user : ".$user."   action : ".$action."   entity : ".$entity);
          }
      }
      catch (AceNotFoundException $e)
      {
        $userSecurityIdentity = UserSecurityIdentity::fromAccount($user); 
        if("MASTER" == $currentUserRight || "OWNER" == $currentUserRight )
        {
          $acl->insertObjectAce(
            $userSecurityIdentity,
            $this->getMask($action)
          );
        } 
        //if not master : set default right to view
        else if("OPERATOR" == $currentUserRight || "CREATE" == $currentUserRight )
        {
          $acl->insertObjectAce(
            $userSecurityIdentity,
            $this->getMask("VIEW")
          );
        }else{ 
            throw new \Exception("   user : ".$user."   action : ".$action."   entity : ".$entity);
          }
      }  
    }
  } 