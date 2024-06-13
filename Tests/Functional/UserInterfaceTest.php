<?php

namespace Spipu\ApiPartnerBundle\Tests\Functional;

use Spipu\CoreBundle\Tests\WebTestCase;
use Spipu\UiBundle\Tests\UiWebTestCaseTrait;

class UserInterfaceTest extends WebTestCase
{
    use UiWebTestCaseTrait;

    public function testMain()
    {
        $client = static::createClient();

        $this->adminLogin($client, 'Admin');

        $client->clickLink('API Partner');
        $crawler = $client->clickLink('API Partner - Swagger');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame("API Partner - Swagger", $crawler->filter('h1')->text());

        $client->clickLink('API Partner');
        $crawler = $client->clickLink('API Partner - Logs');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSame("API Partner - Logs", $crawler->filter('h1')->text());
    }
}
