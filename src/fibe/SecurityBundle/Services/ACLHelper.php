<?php
  namespace fibe\SecurityBundle\Services;

  use Symfony\Component\Security\Core\SecurityContext; 
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\QueryBuilder;
  use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
  use Symfony\Component\Security\Core\Exception\AccessDeniedException;
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

  class ACLHelper
  {

    /*
     * first %s is entityType and second is id
     */
    const CANNOT_FIND_ENTITY_LABEL = 'Cannot find %s %s';
    /*
     * first %s is action, second is entityType and third is id
     */
    const NOT_AUTHORYZED_ENTITY_LABEL = 'You don\'t have the authorization to perform %s on %s %s';

    protected $securityContext;
    protected $entityManager;
    protected $aclProvider;

    public function __construct(SecurityContext $securityContext,EntityManager $entityManager,MutableAclProvider $aclProvider)
    {
      $this->securityContext = $securityContext;
      $this->entityManager   = $entityManager;
      $this->aclProvider     = $aclProvider;
    }

    protected function getUser($id=null)
    {
      if($id){
        return $teamate = $em->getRepository('fibeSecurityBundle:User')->find($id);
      } else
      {
        return $this->securityContext->getToken()->getUser();
      }
      $this->throwNotFoundHttpException( $repositoryName, $id?'#'.$id:'');
    }

    protected function getCurrentConf()
    {
      //TODO redirect to dashboard url with parameter if the conf doesn't exist
      return $this->getUser()->getCurrentConf();
    }

    protected function restrictQueryBuilderByConferenceId(QueryBuilder $queryBuilder)
    {
      $queryBuilder->andWhere("entity.conference = ".$this->getCurrentConf()->getId());
    }

    protected function restrictQueryBuilderByIds(QueryBuilder $queryBuilder,$ids)
    {
      if (is_string($ids)) {
       $queryBuilder->andWhere("entity.id IN ($ids)");
      }
      // No ACL found: deny all
      elseif ($ids===false) {
       $queryBuilder->andWhere("entity.id = 0");
      }
      elseif ($ids===true) {
         // Global-class permission: allow all
      }
    }

    protected function getClassNameByRepositoryName($repositoryName)
    {
      return ACLEntityHelper::ACL_ENTITIES_CLASSPATH.'\\'.$repositoryName;
    }

    protected function throwNotFoundHttpException( $repositoryName, $id)
    {
       throw new NotFoundHttpException(sprintf(ACLHelper::CANNOT_FIND_ENTITY_LABEL, $repositoryName, $id?'#'.$id:''));
    }
  }