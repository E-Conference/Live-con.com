<?php
  namespace fibe\SecurityBundle\Listener;

  use Doctrine\ORM\Event\LifecycleEventArgs; 
  use Symfony\Component\DependencyInjection\ContainerInterface; 
  use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
  use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
  use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity; 
  use Symfony\Component\Security\Acl\Permission\MaskBuilder;
  /**
   * Doctrine listener post persist event filling ACL 
   */
  class AddACL {
    protected $container;

    public function __construct(ContainerInterface $container)
    {
      $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
      $entity = $args->getEntity();
      $entityManager = $args->getEntityManager();
      $token = $this->container->get('security.context')->getToken();
      if (isset($token)) {
        $user = $token->getUser();
      } 
      try {
        //check if the entity is managed with ACL 
        $this->container->get('fibe_security.acl_entity_helper')->getClassNameByRepositoryName($this->get_real_class($entity));
        // creating the ACL
        $aclProvider = $this->container->get('security.acl.provider');

        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl); 
      } catch(\RunTimeException $e){
        // just don't add acl
      }
    }
    /**
     * Obtains an object class name without namespaces
     */
    function get_real_class($obj) {
        $classname = get_class($obj);
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
  }