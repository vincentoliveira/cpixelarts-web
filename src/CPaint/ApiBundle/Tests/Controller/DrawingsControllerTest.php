<?php

namespace CPaint\ApiBundle\Tests\Controller;

use CPaint\DefaultBundle\Tests\CPaintDefaultTestCase;

/**
 * Api Test Controller
 */
class DrawingsControllerTest extends CPaintDefaultTestCase
{
    
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testCompleteScenario()
    {
        // clean database
        $this->cleanDrawings();
        
        $title = "Test 42";
        $color = $position = 42;
        
        // Get all drawings (should get 0 result)
        $this->client->request('GET', '/api/drawings');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/drawings");
        $response1 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEmpty($response1["results"], "Results should be empty.");
        $this->assertEquals(0, $response1["count"], sprintf("Count should be 0, is %d.", $response1["count"]));
        $this->assertEquals(1, $response1["page"], sprintf("Page should be 1, is %d.", $response1["page"]));

        // Get drawing that does not exist
        $this->client->request('GET', '/api/drawings/1');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/drawings/1");

        // create drawing
        $this->client->request('POST', '/api/drawings');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST /api/drawings");
        $rawRepsonseDrawing = $this->client->getResponse()->getContent();
        $responseDrawing = json_decode($rawRepsonseDrawing, true);
        
        $drawingID = $responseDrawing['drawing']['id'];
        
        // create drawing with title and pixel
        $this->client->request('POST', '/api/drawings', array(
            'title' => $title,
            'color' => $color,
            'position' => $position,
        ));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST /api/drawings");
        $rawRepsonseDrawingB = $this->client->getResponse()->getContent();
        $responseDrawingB = json_decode($rawRepsonseDrawingB, true);
        $this->assertEquals($title, $responseDrawingB["drawing"]["title"], sprintf("Tile should be %s, is %d.", $title, $responseDrawingB["drawing"]["title"]));
        $this->assertCount(1, $responseDrawingB["drawing"]["pixels"], sprintf("Should contain 1 pixel."));
        $this->assertEquals($color, $responseDrawingB["drawing"]["pixels"][0]["color"], sprintf("Color should be %.", $color));
        $this->assertEquals($position, $responseDrawingB["drawing"]["pixels"][0]["position"], sprintf("Position should be %.", $color));

         
        // get previously created drawing (should be 0)
        $this->client->request('GET', '/api/drawings/' . $drawingID);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/drawings/" . $drawingID);
        $this->assertEquals($rawRepsonseDrawing, $this->client->getResponse()->getContent());
        
        $this->client->request('GET', '/api/drawings');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/drawings");
        $response2 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(0, $response2["count"], sprintf("Count should be 0, is %d.", $response2["count"]));
        $this->assertEquals(1, $response2["page"], sprintf("Page should be 1, is %d.", $response2["page"]));

        // Set title
        $this->client->request('PATCH', '/api/drawings/' . $drawingID . "/title");
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for PATCH /api/drawings/" . $drawingID . "/title without title");
        
        $this->client->request('PATCH', '/api/drawings/' . $drawingID . "/title", array(
            'title' => $title,
        ));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for PATCH /api/drawings/" . $drawingID . "/title");
        $responseDrawing2 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($title, $responseDrawing2["drawing"]["title"], sprintf("Tile should be %s, is %d.", $title, $responseDrawing2["drawing"]["title"]));

        // Add pixel
        $addPixelUrl = '/api/drawings/' . $drawingID . '/pixels';
        $this->client->request('POST', $addPixelUrl, array(
            'color' => -1,
            'position' => -1,
        ));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST " . $addPixelUrl . " with bad parameters");
        
        $this->client->request('POST', $addPixelUrl, array(
            'color' => $color,
            'position' => $position,
        ));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST " . $addPixelUrl);
        $responseDrawing3 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(1, $responseDrawing3["drawing"]["pixels"], "Should contain 1 pixel.");
        $this->assertEquals($color, $responseDrawing3["drawing"]["pixels"][0]["color"], sprintf("Color should be %.", $color));
        $this->assertEquals($position, $responseDrawing3["drawing"]["pixels"][0]["position"], sprintf("Position should be %.", $position));
        
        // Cannot add pixel over another
        $this->client->request('POST', $addPixelUrl, array(
            'color' => $color,
            'position' => $position,
        ));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST " . $addPixelUrl);

        // Add many pixels
        $this->client->request('POST', $addPixelUrl, array(
            'color' => $color,
            'position' => array($position + 1, $position + 2),
        ));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST " . $addPixelUrl);
        $responseDrawing4 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(3, $responseDrawing4["drawing"]["pixels"], "Should contain 3 pixel.");

        // Lock
        $this->client->request('PATCH', '/api/drawings/' . $drawingID . "/lock");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for PATCH /api/drawings/" . $drawingID . "/lock");
        $responseDrawing5 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseDrawing5["drawing"]["is_locked"], "Should be locked");
        $this->assertTrue($responseDrawing5["drawing"]["is_displayable"], "Should be displayable");
        
        // cannot add pixel to lock drawing
        $this->client->request('POST', $addPixelUrl, array(
            'color' => $color,
            'position' => $position,
        ));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for POST " . $addPixelUrl);
        
        // Get all drawings (should get 1 result)
        $this->client->request('GET', '/api/drawings');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /api/drawings");
        $response3 = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($response3["results"], "Results should be empty.");
        $this->assertEquals(1, $response3["count"], sprintf("Count should be 1, is %d.", $response3["count"]));
        $this->assertEquals(1, $response3["page"], sprintf("Page should be 1, is %d.", $response3["page"]));

    }
}