<?php

/**
 * 
 *
 */

namespace fibe\Bundle\WWWConfBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AuthorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthorRepository extends EntityRepository
{


    public function getRelatedObject($entity = null,$type)
    {
        
     
        $qb = $this->createQueryBuilder('p');

        //Get the paper associate to this relation
        if($type == "paper") {
            $qb ->select('p')
                ->from('IDCISimpleScheduleBundle:CalendarEntityRelation','r')
                ->where('r.id = :entity_id')
                ->setParameter('entity_id',$entity)
            ;
        }else{
             $qb->select('p')
                ->from('relation','r')
                ->from('person','p')
                ->where('r.id = :id_relation')
                ->where('p.id = r.related_to')
                ->setParameter('id_relation',$entity->getId())
            ;

        }

        	$query = $qb->getQuery();
        	$papers = $query->execute();
        return $papers;
    }

    
}
