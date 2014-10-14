<?php

namespace CPaint\DrawingBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Drawing Repository
 */
class DrawingRepository extends EntityRepository
{

    /**
     * Find all query
     * 
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        return $this->createQueryBuilder('drawing')->getQuery();
    }

    /**
     * Find all query
     * 
     * @return \Doctrine\ORM\Query
     */
    public function findAllPaginated($page = 1, $maxResults = 10)
    {
        $qb = $this->createQueryBuilder('drawing');
        
        $count = $qb->select('count(drawing)')->getQuery()->getSingleScalarResult();
        
        $qb->select('drawing');
        $qb->setFirstResult((abs($page) - 1) * $maxResults);
        $qb->setMaxResults($maxResults);
        $results = $qb->getQuery()->getArrayResult();
        
        return array(
            'results' => $results,
            'count' => intval($count),
            'page' => $page,
        );
    }
    
}