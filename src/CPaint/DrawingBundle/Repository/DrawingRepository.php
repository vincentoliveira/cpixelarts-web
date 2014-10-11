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
    
}