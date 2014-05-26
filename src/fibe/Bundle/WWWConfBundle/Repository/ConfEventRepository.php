<?php

namespace fibe\Bundle\WWWConfBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ConfEventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConfEventRepository extends EntityRepository
{


     public function filtering($params, $currentConf){
    
        $entities = array();
        $qb = $this->getAllOrderByStartAtQueryBuilder();
        $qb     
          ->where('confevent.conference = :conference_id')
          ->setParameter('conference_id', $currentConf->getId());

        if(isset($params['only_instant'])) {
            $qb
                ->andWhere('confevent.isInstant = 1')
            ;
        }else{
            $qb
                ->andWhere('confevent.isInstant = 0')
            ;
        }

       if(isset($params['summary'])) {
           $qb
                ->andWhere('confevent.id = :summary')
                ->setParameter('summary', $params['summary'])
            ;
        }

        if(isset($params['location'])) {
            $qb
                ->leftJoin('confevent.location', 'loc')
                ->andWhere('loc.id = :loc')
                ->setParameter('loc', $params['location'])
            ;
        }
        if(isset($params['category'])) {
            $qb
                ->leftJoin('confevent.categories', 'cat')
                ->andWhere('cat.id = :cat_id')
                ->setParameter('cat_id', $params['category'])
            ;
        }


        $query = $qb->getQuery();
        return  $query->execute();

    }


     /**
     * getRelatedAvailableCalendarEntitiesQueryBuilder
     *
     * @param $entity CalendarEntity
     * @return QueryBuilder
     */
    public function getRelatedAvailableCalendarEntitiesQueryBuilder($entity = null)
    {
        $qb = $this->getAllOrderByStartAtQueryBuilder();

        if($entity) {
            $ids = array();
            foreach($entity->getRelateds() as $related) {
                $ids[] = $related->getRelatedTo()->getId();
            }

            $qb
                ->where('confevent.id not in (:entities_id)')
                ->setParameter('entities_id', array_merge(
                    array($entity->getId()),
                    $ids
                ))
            ;
        }

        return $qb;
    }

    /**
     * getRelatedAvailableCalendarEntitiesQuery
     *
     * @param $entity CalendarEntity
     * @return Query
     */
    public function getRelatedAvailableCalendarEntitiesQuery($entity = null)
    {
        $qb = $this->getRelatedAvailableCalendarEntitiesQueryBuilder($entity);

        return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * getRelatedAvailableCalendarEntities
     *
     * @param $entity CalendarEntity
     * @return DoctrineCollection
     */
    public function getRelatedAvailableCalendarEntities($entity = null)
    {
        $q = $this->getRelatedAvailableCalendarEntitiesQuery($entity);

        return is_null($q) ? array() : $q->getResult();
    }

    /**
     * getAllOrderByStartAtQueryBuilder
     *
     * @return QueryBuilder
     */
    public function getAllOrderByStartAtQueryBuilder()
    {
        $qb = $this->createQueryBuilder('confevent');
        $qb
            ->orderBy('confevent.startAt', 'ASC')
            ->addOrderBy('confevent.location', 'ASC')
        ;

        return $qb;
    }

    /**
     * getAllOrderByStartAtQuery
     *
     * @return Query
     */
    public function getAllOrderByStartAtQuery()
    {
        $qb = $this->getAllOrderByStartAtQueryBuilder();

        return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * getAllOrderByStartAt
     *
     * @return DoctrineCollection
     */
    public function getAllOrderByStartAt()
    {
        $q = $this->getAllOrderByStartAtQuery();

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
        $qb = $this->getAllOrderByStartAtQueryBuilder();

        if(isset($params['only_instant'])) {
            $qb
                ->andWhere('confevent.isInstant = true')
            ;
        }else{
            $qb
                ->andWhere('confevent.isInstant = false')
            ;
        }

        if(isset($params['id'])) {
            $qb
                ->andWhere('confevent.id = :id')
                ->setParameter('id', $params['id'])
            ;
        }

        if(isset($params['ids'])) {
            $qb
                ->andWhere($qb->expr()->in('confevent.id', $params['ids']))
            ;
        } 

        if(isset($params['before'])) {
            $qb
                ->andWhere('confevent.startAt < :before')
                ->setParameter('before', new \DateTime($params['before']))
            ;
        }

        if(isset($params['after'])) {
            $qb
                ->andWhere('confevent.endAt > :after')
                ->setParameter('after', new \DateTime($params['after']))
            ;
        }
        
        if(isset($params['conference_id'])) {
            $qb
                ->andWhere('confevent.conference = :conference_id')
                ->setParameter('conference_id', $params['conference_id'])
            ;
        }

        if(isset($params['category_id'])) {
            $qb
                ->leftJoin('confevent.categories', 'c')
                ->andWhere('c.id = :cat_id')
                ->setParameter('cat_id', $params['category_id'])
            ;
        }

        if(isset($params['category_ids'])) {
            $qb
                ->leftJoin('confevent.categories', 'cs')
                ->andWhere($qb->expr()->in('cs.id', $params['category_ids']))
            ;
        }

        if(isset($params['category_name'])) {
            $qb
                ->leftJoin('confevent.categories', 'c')
                ->andWhere('c.name = :category_name')
                ->setParameter('category_name', $params['category_name'])
            ;
        }

        if(isset($params['parent_category_id'])) {
            $qb
                ->leftJoin('confevent.categories', 'pc')
                ->andWhere('pc.parent = :parent_id')
                ->setParameter('parent_id', $params['parent_category_id'])
            ;
        }

        if(isset($params['parent_category_ids'])) {
            $qb
                ->leftJoin('confevent.categories', 'pcs')
                ->andWhere($qb->expr()->in('pcs.parent', $params['parent_category_ids']))
            ;
        }

        if(isset($params['ancestor_category_id'])) {
            $qb
                ->leftJoin('confevent.categories', 'pc')
                ->andWhere($qb->expr()->like('pc.tree', sprintf(
                    "'%%%d%s'",
                    $params['ancestor_category_id'],
                    Category::getTreeSeparator()
                )))
            ;
        }

        if(isset($params['ancestor_category_ids'])) {
            $qb->leftJoin('confevent.categories', 'pcs');
            $temp = array();
            foreach($params['ancestor_category_ids'] as $id) {
                $temp[] = $qb->expr()->like('pcs.tree', sprintf(
                    "'%%%d%s'",
                    $id,
                    Category::getTreeSeparator()
                ));
            }
            $qb->andWhere(call_user_func_array(array($qb->expr(),'orx'), $temp));
        }

        if(isset($params['location_id'])) {
            $qb
                ->andWhere('confevent.location = :location_id')
                ->setParameter('location_id', $params['location_id'])
            ;
        }

        if(isset($params['location_ids'])) {
            $qb
                ->andWhere($qb->expr()->in('confevent.location', $params['location_ids']))
            ;
        }

        if(isset($params['xproperty_namespace'])) {
            $qb
                ->leftJoin('confevent.xProperties', 'xpn')
                ->andWhere('xpn.xNamespace = :xproperty_namespace')
                ->setParameter('xproperty_namespace', $params['xproperty_namespace'])
            ;
        }

        if(isset($params['xproperty_key'])) {
            $qb
                ->leftJoin('confevent.xProperties', 'xpk')
                ->andWhere('xpk.xKey = :xproperty_key')
                ->setParameter('xproperty_key', $params['xproperty_key'])
            ;
        }

        if(isset($params['xproperty_value'])) {
            $qb
                ->leftJoin('confevent.xProperties', 'xpv')
                ->andWhere('xpv.xValue = :xproperty_value')
                ->setParameter('xproperty_value', $params['xproperty_value'])
            ;
        }

        if(isset($params['parent_xproperty_value'])) {
            $qb
                ->leftJoin('confevent.parent', 'parent')
                ->leftJoin('parent.xProperties','parentxp')
                ->andWhere('parentxp.xValue = :parent_xproperty_value')
                ->setParameter('parent_xproperty_value', $params['parent_xproperty_value'])
            ;
        }

        if(isset($params['child_xproperty_value'])) {
            $qb
                ->leftJoin('confevent.children', 'child')
                ->leftJoin('child.xProperties','childxp')
                ->andWhere('childxp.xValue = :child_xproperty_value')
                ->setParameter('child_xproperty_value', $params['child_xproperty_value'])
            ;
        }


        if(isset($params['location_name'])) {
            $qb
                ->leftJoin('confevent.location', 'lct')
                ->andWhere('lct.name = :location_name')
                ->setParameter('location_name', $params['location_name'])
            ;
        }
        

        if(isset($params['parent_id'])) {
            $qb
                ->andWhere('confevent.parent = :parent_id')
                ->setParameter('parent_id', $params['parent_id'])
            ;
        }

        if(isset($params['child_id'])) {
            $qb
                ->leftJoin('confevent.children', 'child')
                ->andWhere('child.id = :child_id')
                ->setParameter('child_id', $params['child_id'])
            ;
        }

        if(isset($params['theme_id'])) {
            $qb
                 ->leftJoin('confevent.themes', 't')
                 ->andWhere('t.id = :theme_id')
                 ->setParameter('theme_id',$params['theme_id']);

            ;
        }

        if(isset($params['topic_ids'])) {
            $qb 
                 ->leftJoin('confevent.topics', 't')
                 ->andWhere($qb->expr()->in('t.id', $params['topic_ids'])) 
            ;
        }

        if(isset($params['topic_name'])) {
            $qb
                 ->leftJoin('confevent.topics', 't')
                 ->andWhere('t.libelle = :topic_name')
                 ->setParameter('topic_name',$params['topic_name']);

            ;
        }

        if(isset($params['person_id'])) {
            $qb
                ->leftJoin('confevent.roles', 'r')
                ->leftJoin('r.person', 'p')
                ->andWhere('p.id = :person_id')
                ->setParameter('person_id',$params['person_id']);
            ;
        }

        if(isset($params['person_slug'])) {
            $qb
                ->leftJoin('confevent.roles', 'r')
                ->leftJoin('r.person', 'p')
                ->andWhere('p.slug = :person_slug')
                ->setParameter('person_slug',$params['person_slug']);
            ;
        }

        if(isset($params['role_type'])) {
            $qb
                ->leftJoin('confevent.roles', 'ro')
                ->leftJoin('ro.type', 'rot')
                ->andWhere('rot.name = :role_type')
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
