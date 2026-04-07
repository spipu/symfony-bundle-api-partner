<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Spipu\ApiPartnerBundle\Service\ApiControllerService;
use Spipu\CoreBundle\Tests\WebTestCase;
use Spipu\UiBundle\Tests\UiWebTestCaseTrait;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ApiControllerService::class)]
class UserInterfaceTest extends WebTestCase
{
    use UiWebTestCaseTrait;

    public function testMain(): void
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
