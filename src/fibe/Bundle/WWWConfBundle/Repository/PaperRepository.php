<?php

/**
 * 
 *
 */

namespace fibe\Bundle\WWWConfBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PaperRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaperRepository extends EntityRepository
{


    public function getPaperForSelect($entity = null)
    {
        //Recuperation des ids des paper etant deja lié a l'event
        $papers_id= array();
        if($entity){
            foreach($entity->getPapers() as $paper){
                array_push($papers_id,$paper->getId());
            }
        }
       
     
        $qb = $this->createQueryBuilder('p');
            $qb ->select(array('p.id','p.title'))
                ->andWhere($qb->expr()->notIn('p.id',$papers_id))
            ;
        	$query = $qb->getQuery();
        	$papers = $query->execute();
        return $papers;
    }

    
}
