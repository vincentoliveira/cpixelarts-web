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
        return $this->createQueryBuilder('drawing')
                ->leftJoin('drawing.pixels', 'pixel')
                ->orderBy('drawing.id', 'DESC')
                ->getQuery();
    }

    /**
     * Find all query
     * 
     * @return \Doctrine\ORM\Query
     */
    public function findAllPaginated($page = 1, $maxResults = 10)
    {
        $qb = $this->createQueryBuilder('drawing');
        
        $count = $qb->select('COUNT(drawing)')->getQuery()->getSingleScalarResult();
        
        $qb->select('drawing, COUNT(pixel), COUNT(DISTINCT pixel.color)');
        $qb->leftJoin('drawing.pixels', 'pixel');
        $qb->groupBy('drawing.id');
        $qb->orderBy('drawing.id', 'DESC');
        $qb->setFirstResult((abs($page) - 1) * $maxResults);
        $qb->setMaxResults($maxResults);
        $rawResults = $qb->getQuery()->getArrayResult();
        
        $results = array();
        foreach ($rawResults as $result) {
            $result[0]['nb_pixels'] = $result[1];
            $result[0]['nb_colors'] = $result[2];
            $results[] = $result[0];
        }
        
        return array(
            'results' => $results,
            'count' => intval($count),
            'page' => $page,
        );
    }
    
}