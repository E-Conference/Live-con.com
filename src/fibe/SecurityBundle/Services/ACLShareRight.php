<?php
  namespace fibe\SecurityBundle\Services;

  use Symfony\Component\Security\Core\SecurityContext; 
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\QueryBuilder;
  use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;
  use fibe\SecurityBundle\Services\ACLEntityHelper;
  use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

  /** 
   */
  class ACLShareRight extends ACLEntityHelper
  {
    /**
     * [allowUserACL description]
     * @param  [type] $action         [description]
     * @param  [type] $teamateId      the choosen teamate id
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             [description]
     * @return [type]                 [description]
     */
    public function updateUserACL($teamateId,$action,$repositoryName,$id)
    {
      $entity = $this->getEntityACL($action,$repositoryName,$id);
      if (!$entity)
      {
        throw new NotFoundHttpException(sprintf(ACLHelper::CANNOT_FIND_ENTITY_LABEL,
          $repositoryName,
          $id?'#'.$id:''
        ));
      } 

      $action =  getMask($action);
      $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
      $teamateSecurityIdentity = UserSecurityIdentity::fromAccount($this->getUser($teamateId)); 

      $this->updateUserACLs($teamateSecurityIdentity,$action,$entitySecurityIdentity);
    }
    /**
     * [allowUserACL description]
     * @param  [type] $action         [description]
     * @param  [type] $teamateId      the choosen teamate id
     * @param  [type] $repositoryName [description]
     * @param  [type] $id             [description]
     * @return [type]                 [description]
     */
    public function updateUserACLs($teamateId,$action,$repositoryName)
    {
      $entities = $this->getEntitiesACL($action,$repositoryName); 
      $action =  getMask($action);
      $teamateSecurityIdentity = UserSecurityIdentity::fromAccount($this->getUser($teamateId)); 

      foreach ($enties as $entity) {
        $entitySecurityIdentity = ObjectIdentity::fromDomainObject($entity);
        $this->updateUserACLs($teamateSecurityIdentity,$action,$entitySecurityIdentity);
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
        foreach($acl->getObjectAces() as $index => $ace)
        {
          $aceSecurityId = $ace->getSecurityIdentity();
          if($aceSecurityId ->equals($teamateSecurityIdentity))
          {
            $acl->updateObjectAce(
              $index,
              $action
            );
          }
        }
        $aclProvider->updateAcl($acl);
      } catch (AclNotFoundException $e)
      {
        // No existing ACL found so create a new one
        $acl = $aclProvider->createAcl($entitySecurityIdentity);
        $acl->insertObjectAce(
          $teamateSecurityIdentity,
          $action
        );
        $aclProvider->updateAcl($acl);
      }
    }

    private function getMask($mask)
    {
      if (!defined($mask = 'Symfony\Component\Security\Acl\Permission\MaskBuilder::MASK_'.$mask))
      {
          throw new \RuntimeException('There was no code defined for mask '.$mask.'!');
      }
      return $mask;
    }
  }