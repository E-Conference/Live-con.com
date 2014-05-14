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
  use fibe\SecurityBundle\Services\ACLEntityHelper;
  use fibe\SecurityBundle\Entity\UserConfPermission;
  use fibe\SecurityBundle\Entity\ConfPermission;

  /** 
   */
  class ACLUserPermissionHelper extends ACLEntityHelper
  {

    //get or create a default UserConfPermission object for the given user or the current user

    public function getUserConfPermission($manager=null)
    {
      $currentConf = $this->getCurrentConf();

      $userConfPermission = new UserConfPermission();
      if($manager)$userConfPermission->setUser($manager);

      $entity = $currentConf;
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Conference');
      $confPermission->setAction(!$manager ? 'EDIT' : $this->getACEByEntity($entity,$manager));
      $confPermission->setRepositoryName('WwwConf');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);

      $entity = $currentConf->getAppConfig();
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Mobile application');
      $confPermission->setAction(!$manager ? 'EDIT' : $this->getACEByEntity($entity,$manager));
      $confPermission->setRepositoryName('MobileAppConfig');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);

      $entity = $currentConf->getTeam();
      $confPermission = new ConfPermission();
      $confPermission->setEntityLabel('Team');
      $confPermission->setAction(!$manager ? 'VIEW' : $this->getACEByEntity($entity,$manager));
      $confPermission->setRepositoryName('Team');
      $confPermission->setEntityId($entity->getId());
      $userConfPermission->addConfPermission($confPermission);

 

      return $userConfPermission; 
    }

    public function updateUserConfPermission(UserConfPermission $userConfPermission)
    {
      if($userConfPermission->getUser()->getId() == $this->getUser()->getId())
      {
        throw new AccessDeniedException("You cannot demote yourself.");
      }
      //TODO : 
      foreach ($userConfPermission->getConfPermissions() as $confPermission)
      {
        $repositoryName = $confPermission->getRepositoryName();
        $action = $confPermission->getAction();
        $id=$confPermission->getEntityId();
        $entity = $this->getEntityACL($action,$repositoryName,$id);
        $this->updateUserACL($userConfPermission->getUser(), $action, $repositoryName, $id);
      }
    }

    /**
     * [allowUserACL description]
     * @param  [type] $action         [description]
     * @param  [type] $teamateId      the choosen teamate id
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             [description]
     */
    private function updateUserACL($teamateId,$action,$repositoryName,$id)
    {
      $entity = $this->getEntityACL($action,$repositoryName,$id);
      if (!$entity)
      {
        $this->throwNotFoundHttpException( $repositoryName, $id); 
      } 

      $action =  $this->getMask($action);
      $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
      $teamateSecurityIdentity = UserSecurityIdentity::fromAccount($this->getUser($teamateId)); 

      $this->performUpdateUserACL($teamateSecurityIdentity,$action,$entitySecurityIdentity);
    }
    /**
     * [allowUserACL description]
     * @param  [type] $action         [description]
     * @param  [type] $teamateId      the choosen teamate id
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             [description] 
     */
    private function updateUserACLs($teamateId,$action,$repositoryName)
    {
      $entities = $this->getEntitiesACL($action,$repositoryName); 
      $action =  $this->getMask($action);
      $teamateSecurityIdentity = UserSecurityIdentity::fromAccount($this->getUser($teamateId)); 

      foreach ($enties as $entity)
      {
        $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
        $this->performUpdateUserACL($teamateSecurityIdentity,$action,$entitySecurityIdentity);
      }
    }

    private function performUpdateUserACL($teamateSecurityIdentity,$action,$entitySecurityIdentity)
    {
      try
      {
        $acl = $this->aclProvider->findAcl(
          $entitySecurityIdentity,
          array($teamateSecurityIdentity)
        );
        $found=false;
        foreach($acl->getObjectAces() as $index => $ace)
        {
          $aceSecurityId = $ace->getSecurityIdentity();
          if(!$found && $aceSecurityId ->equals($teamateSecurityIdentity))
          {
            $found=true;
            $acl->updateObjectAce(
              $index,
              $action
            );
          }
        }
        if(!$found){
          $acl->insertObjectAce(
            $teamateSecurityIdentity,
            $action
          );  
        }
        $this->aclProvider->updateAcl($acl);
      } catch (AclNotFoundException $e)
      {
        // No existing ACL found so create a new one
        $acl = $this->aclProvider->createAcl($entitySecurityIdentity);
        $acl->insertObjectAce(
          $teamateSecurityIdentity,
          $action
        );
        $this->aclProvider->updateAcl($acl);

      }
    }
  }