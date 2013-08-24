<?

// http://symfony.com/fr/doc/2.2/cookbook/doctrine/event_listeners_subscribers.html
// http://docs.doctrine-project.org/en/2.1/reference/events.html#preupdate

namespace fibe\Bundle\WWWConfBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\HttpFoundation\Session\Session;
use fibe\Bundle\WWWConfBundle\Entity\ConfEvent as Event;

class updateChildrenDate
{ 
    const DIFF_FORMAT = '%Y%M%D%H%I%S';
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
                      
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        // $entity = $args->getEntity();
        // $em = $args->getEntityManager();
        // $uow = $em->getUnitOfWork();

        // // peut-être voulez-vous seulement agir sur une entité « Product »
        // if ($entity instanceof Event) {
        //     echo("preUpdate:");

        //     if (!($args->hasChangedField($fieldName))) {
        //         continue;
        //     }

        //     $fieldName = 'startAt';  

        //     $oldStartAt = $args->getOldValue($fieldName);
        //     $newStartAt = $args->getNewValue($fieldName);
        //     $this->session->set('diff', date_diff($newStartAt, $oldStartAt)->format(self::DIFF_FORMAT));
        //     $this->session->set('isBefore', $newStartAt < $oldStartAt);
        //     $this->isBefore = $newStartAt < $oldStartAt ;
        //     // foreach ($entity->getChildren() as $child) { 
        //     //     // var_dump($child->getSummary()); 

        //     //     if( $newStartAt > $oldStartAt ) {
        //     //         $child->setStartAt(date_add($child->getStartAt(), $this->$diff)); 
        //     //         $child->setEndAt(date_add($child->getEndAt(), $this->$diff)); 
        //     //     }else{ 
        //     //         $child->setStartAt(date_sub($child->getStartAt(), $this->$diff)); 
        //     //         $child->setEndAt(date_sub($child->getEndAt(), $this->$diff)); 
        //     //     }
        //     //     $em->persist($child);
        //     // }   
        // }
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
        {

    //     $em = $eventArgs->getEntityManager();
    //     $uow = $em->getUnitOfWork();
    //     echo("onFlush:");


    //     foreach ($uow->getScheduledEntityUpdates() as $entity) { 

    //         //only Event
    //         if (!($entity instanceof Event)) {
    //             continue;
    //         }

    //         $fieldName = 'startAt';  
    //         $changeset = $uow->getEntityChangeSet($entity);

    //         //only startAt
    //                     var_dump("CHILD");
    //         if (isset($changeset[$fieldName])){
 
    //             //compute diff 
    //             $oldStartAt =  $changeset[$fieldName][0] ;
    //             $newStartAt =  $changeset[$fieldName][1] ;
    //             $diff = date_diff($newStartAt, $oldStartAt);
    //             $isBefore = $newStartAt < $oldStartAt;
    
    //                     var_dump("CHILD");
    //             foreach ($entity->getChildren() as $child) { 
    //                     var_dump("CHILD");
    //                  // var_dump($child->getSummary()); 

    //                 if( $isBefore == true ) { 
    //                     $child->setStartAt(date_sub($child->getStartAt(), $diff)); 
    //                     $child->setEndAt(date_sub($child->getEndAt(), $diff)); 
    //                     $child->setStartAt(date_sub($child->getStartAt(), $diff)); 
    //                     $child->setEndAt(date_sub($child->getEndAt(), $diff)); 
    //                 }elseif( $isBefore == false ) {
    //                     $child->setStartAt(date_add($child->getStartAt(), $diff)); 
    //                     $child->setEndAt(date_add($child->getEndAt(), $diff)); 
    //                     $child->setStartAt(date_add($child->getStartAt(), $diff)); 
    //                     $child->setEndAt(date_add($child->getEndAt(), $diff)); 
    //                 }else{
    //                     var_dump("CACA");

    //                 }
    //                 $em->persist($child);

    //                 $classMetadata = $em->getClassMetadata(get_class($child)); 
    //                 $uow->recomputeSingleEntityChangeSet($classMetadata, $child); // We need to manually tell EM to notice the changes
    //                 $uow->computeChangeSet($classMetadata, $child); // We need to manually tell EM to notice the changes
    //                 var_dump($child->getStartAt());
    //             }  
    //                 $em->persist($entity);
    //             $classMetadata = $em->getClassMetadata(get_class($entity)); 
    //             $uow->computeChangeSet($classMetadata, $entity); // We need to manually tell EM to notice the changes
    //             $uow->recomputeSingleEntityChangeSet($classMetadata, $entity); // We need to manually tell EM to notice the changes
    //             $uow->computeChangeSets();

    //             // $blockVersion = new BlockVersion(); 
    //             // $blockVersion->setContent(); // $changeset['content'] = array('old value', 'new value') 
    //             // $blockVersion->setBlock($entity); 
    //             // $em->persist($blockVersion); 
    //         }

                
    //         // $em->persist($entity);

    //         // $classMetadata = $em->getClassMetadata(get_class($entity));
    //         // $uow->recomputeSingleEntityChangeSet($classMetadata, $entity); 
    //     }
    }
}