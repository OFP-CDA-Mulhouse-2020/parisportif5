<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DummyFormControllerTest extends WebTestCase
{
    public function testFormPage()
    {
        $client = static::createClient();

        $client->request('GET', '/account/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
