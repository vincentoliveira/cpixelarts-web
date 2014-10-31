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
                        ->where('drawing.isDisplayable = true')
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

        $count = $qb->select('COUNT(drawing)')
                ->where('drawing.isDisplayable = true')
                ->getQuery()
                ->getSingleScalarResult();

        $qb->select('drawing, COUNT(pixel), COUNT(DISTINCT pixel.color)')
                ->where('drawing.isDisplayable = true')
                ->leftJoin('drawing.pixels', 'pixel')
                ->groupBy('drawing.id')
                ->orderBy('drawing.id', 'DESC')
                ->setFirstResult((abs($page) - 1) * $maxResults)
                ->setMaxResults($maxResults);
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
