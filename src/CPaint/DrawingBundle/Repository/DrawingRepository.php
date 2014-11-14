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
            $drawing = array(
                'id'                => $result[0]['id'],
                'title'             => $result[0]['title'],
                'title_canonical'   => $result[0]['titleCanonical'],
                'created_at'        => $result[0]['createdAt'],
                'is_locked'         => $result[0]['isLocked'],
                'width'             => $result[0]['width'],
                'height'            => $result[0]['height'],
                'nb_pixels'         => intval($result[1]),
                'nb_colors'         => intval($result[2]),
            );
            $results[] = $drawing;
        }

        return array(
            'results' => $results,
            'count' => intval($count),
            'page' => $page,
        );
    }

}
