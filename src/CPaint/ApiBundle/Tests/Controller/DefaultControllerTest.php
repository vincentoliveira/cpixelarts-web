<?php

namespace CPaint\ApiBundle\Tests\Controller;

use CPaint\DefaultBundle\Tests\CPaintDefaultTestCase;

/**
 * Api Test Controller
 */
class DefaultControllerTest extends CPaintDefaultTestCase
{
    
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testVersionScenario()
    {
        $version = $this->container->getParameter('api_version');
        $this->client->request('GET', '/api/version');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/version");
        $this->assertRegExp("/$version/", $this->client->getResponse()->getContent(), "Bad version for GET /api/version");

        $this->client->request('GET', '/api/version.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/version.json");
        $this->assertRegExp("/$version/", $this->client->getResponse()->getContent(), "Bad version for GET /api/version.json");

        $this->client->request('GET', '/api/version.xml');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/version.xml");
        $this->assertRegExp("/$version/", $this->client->getResponse()->getContent(), "Bad version for GET /api/version.xml");
        
    }
}