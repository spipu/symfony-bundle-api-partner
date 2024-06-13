<?php

namespace Spipu\ApiPartnerBundle\Tests\Functional;

use Spipu\ConfigurationBundle\Service\ConfigurationManager;
use Spipu\CoreBundle\Tests\WebTestCase;
use Spipu\UiBundle\Tests\UiWebTestCaseTrait;

class ApiTest extends WebTestCase
{
    use UiWebTestCaseTrait;
    use ApiTestTrait;

    public function testMain()
    {
        $apiKey = 'FAKE_api_key';
        $apiSecret = 'FAKE_api_secret';

        $this->setPartnerConfigurationCredentials(true, $apiKey, $apiSecret);

        $this->setPartnerProfile('/api', $apiKey, $apiSecret);

        $client = static::createClient();

        $this->callApiPartner($client, 'GET', '/hello_world', ['name' => 'foo']);
        $result = $this->assertApiPartnerSuccessText($client);
        $this->assertSame('Hello World [foo] from [Api User]', $result);

        $this->callApiPartner($client, 'GET', '/version');
        $result = $this->assertApiPartnerSuccessJson($client);
        $this->assertArrayHasKey('version', $result);
    }

    protected function setPartnerConfigurationCredentials(bool $enabled, string $apiKey, string $apiSecret): void
    {
        $configurationManager = $this->getContainer()->get(ConfigurationManager::class);
        $configurationManager->set('api.partner.enabled', $enabled);
        $configurationManager->set('api.partner.api_key', $apiKey);
        $configurationManager->setEncrypted('api.partner.api_secret', $apiSecret);
    }
}
