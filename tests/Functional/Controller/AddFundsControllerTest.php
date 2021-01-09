<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddFundsControllerTest extends WebTestCase
{
    public function testIfPageIsDisplayed()
    {
        $client = static::createClient();

        $client->request('GET', '/account/addfunds');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
