<?php

namespace CPaint\DefaultBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of IOTestCase
 */
abstract class CPaintDefaultTestCase extends WebTestCase
{

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp()
    {
        parent::setUp();

        $this->client = static::createClient(array('environment' => 'test'));
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * Remove all drawings from database
     */
    protected function cleanDrawings()
    {
        $tableNames = [];
        
        $cmdDrawing = $this->em->getClassMetadata("CPaintDrawingBundle:Drawing");
        $tableNames[] = $cmdDrawing->getTableName();
        
        $cmdPixel = $this->em->getClassMetadata("CPaintDrawingBundle:Pixel");
        $tableNames[] = $cmdPixel->getTableName();
        
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tableNames as $tableName) {
                $q = $dbPlatform->getTruncateTableSql($tableName);
                $connection->executeUpdate($q);
            }
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

}
