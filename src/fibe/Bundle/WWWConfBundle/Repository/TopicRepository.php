<?php

  namespace fibe\Bundle\WWWConfBundle\Repository;

  use Doctrine\ORM\EntityRepository;

  /**
   * TopicRepository
   *
   * This class was generated by the Doctrine ORM. Add your own custom
   * repository methods below.
   */
  class TopicRepository extends EntityRepository
  {
    /**
     * getOrderedQueryBuilder
     *
     * @return Object QueryBuilder
     */
    public function getOrderedQueryBuilder()
    {
      $qb = $this->createQueryBuilder('topic');
      $qb->orderBy('topic.name', 'ASC');

      return $qb;
    }

    /**
     * getOrderedQuery
     *
     * @return Object Query
     */
    public function getOrderedQuery()
    {
      $qb = $this->getOrderedQueryBuilder();

      return is_null($qb) ? $qb : $qb->getQuery();
    }

    /**
     * getOrdered
     *
     * @return Object DoctrineCollection
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
     *
     * @return Object QueryBuilder
     */
    public function extractQueryBuilder($params)
    {
      $qb = $this->getOrderedQueryBuilder();

      if (isset($params['id']))
      {
        $qb
          ->andWhere('topic.id = :id')
          ->setParameter('id', $params['id']);
      }

      if (isset($params['ids']))
      {
        $qb
          ->andWhere($qb->expr()->in('topic.id', $params['ids']));
      }

      return $qb;
    }

    /**
     * extractQuery
     *
     * @param array $params
     *
     * @return Object Query
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
     *
     * @return Object DoctrineCollection
     */
    public function extract($params)
    {
      $q = $this->extractQuery($params);

      return is_null($q) ? array() : $q->getResult();
    }


    /**
     * @TODO comment
     *
     * @param $params
     * @param $currentConf
     *
     * @return mixed
     */
    public function filtering($params, $currentConf)
    {

      $entities = array();
      $qb = $this->createQueryBuilder('t');
      $qb
        ->where('t.conference = :conference_id')
        ->setParameter('conference_id', $currentConf->getId());

      if (isset($params['id']))
      {
        $qb
          ->andWhere('t.id = :id')
          ->setParameter('id', $params['id']);
      }

      if (isset($params['paper']))
      {
        $qb
          ->leftJoin('t.papers', 'p')
          ->andWhere('p.id = :paper_id')
          ->setParameter('paper_id', $params['paper']);
      }

      $query = $qb->getQuery();
      return $query->execute();

    }
  }
