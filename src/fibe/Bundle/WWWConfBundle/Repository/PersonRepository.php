<?php

/**
 * 
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @licence: GPL
 *
 */

namespace fibe\Bundle\WWWConfBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LocationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *  
 */
class PersonRepository extends EntityRepository
{
    
    public function getAuthor($paper)
    {
        

         $query = $this->getEntityManager()->createQuery(
            'SELECT p
             FROM fibeWWWConfBundle:Person p, fibeWWWConfBundle:Author a
             WHERE a.id_paper = :id_paper
             AND   p.id = a.id_person'
            )->setParameter('id_paper', $paper->getId());

            $authors = $query->getResult();
          
        return $authors; 
    }


    /**
     * getOrderedQueryBuilder
     *
     * @return QueryBuilder
     */
    public function getOrderedQueryBuilder()
    {
        $qb = $this->createQueryBuilder('loc');
        $qb->orderBy('loc.name', 'ASC');

        return $qb;
    }

    /**
     * getOrderedQuery
     *
     * @return Query
     */
    public function getOrderedQuery()
    {
        $qb = $this->getOrderedQueryBuilder();

        return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * getOrdered
     *
     * @return DoctrineCollection
     */
    public function getOrdered()
    {
        $q = $this->getOrderedQuery();

        return is_null($q) ? array() : $q->getResult();
    }

    /**
     * extractQueryBuilder
     *
     * @param array $params
     * @return QueryBuilder
     */
    public function extractQueryBuilder($params)
    {
        $qb = $this->getOrderedQueryBuilder();

        if(isset($params['id'])) {
            $qb
                ->andWhere('loc.id = :id')
                ->setParameter('id', $params['id'])
            ;
        }

        if(isset($params['ids'])) {
            $qb
                ->andWhere($qb->expr()->in('loc.id', $params['ids']))
            ;
        }

        if(isset($params['slug'])) {
            $qb
                ->andWhere('loc.slug = :slug')
                ->setParameter('slug', $params['slug'])
            ;
        }

        if(isset($params['event_id'])) {
            $qb
                ->leftJoin('loc.roles', 'r')
                ->andWhere('r.event = :ev_id')
                ->setParameter('ev_id', $params['event_id'])
            ;
        }

        if(isset($params['event_uri'])) {
            $qb
                ->leftJoin('loc.roles', 'ro')
                ->leftJoin('ro.event', 'ev')
                ->leftJoin('ev.xProperties', 'xpv')
                ->andWhere('xpv.xValue = :xproperty_value')
                ->setParameter('xproperty_value', $params['event_uri'])
            ;
        }

        if(isset($params['role_type'])) {
            $qb
                ->leftJoin('loc.roles', 'rol')
                ->leftJoin('rol.type', 'rolt')
                ->andWhere('rolt.name = :role_type')
                ->setParameter('role_type',$params['role_type']);
            ;
        }
 
        return $qb;
    }

    /**
     * extractQuery
     *
     * @param array $params
     * @return Query
     */
    public function extractQuery($params)
    {
        $qb = $this->extractQueryBuilder($params);

        return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * extract
     *
     * @param array $params
     * @return DoctrineCollection
     */
    public function extract($params)
    {
        $q = $this->extractQuery($params);

        return is_null($q) ? array() : $q->getResult();
    }
}
