<?php

namespace Spipu\ApiPartnerBundle\Tests\Functional;

use Spipu\ConfigurationBundle\Service\ConfigurationManager;
use Spipu\CoreBundle\Tests\WebTestCase;
use Spipu\UiBundle\Tests\UiWebTestCaseTrait;

class ApiTest extends WebTestCase
{
    use UiWebTestCaseTrait;
    use ApiTestTrait;

    protected string $apiEndPointUrl = '/api';

    public function testMainOk()
    {
        $apiKey = md5('good_api_key');
        $apiSecret = md5('good_api_secret');

        $this->setPartnerConfigurationCredentials(true, $apiKey, $apiSecret);

        $this->setPartnerProfile($this->apiEndPointUrl, $apiKey, $apiSecret);

        $client = static::createClient();

        $this->callApiPartner($client, 'GET', '/hello_world', ['name' => 'foo']);
        $result = $this->assertApiPartnerSuccessText($client);
        $this->assertSame('Hello World [foo] from [Api User]', $result);

        $this->callApiPartner($client, 'GET', '/version');
        $result = $this->assertApiPartnerSuccessJson($client);
        $this->assertArrayHasKey('version', $result);
    }

    public function testMainKo()
    {
        $apiKey = md5('good_api_key');
        $apiSecret = md5('good_api_secret');

        $this->setPartnerConfigurationCredentials(true, $apiKey, $apiSecret);
        $this->setPartnerProfile($this->apiEndPointUrl, $apiKey, $apiSecret);

        $client = static::createClient();

        // ERROR 1001 - API Key header is missing => 404
        $client->request('GET', $this->apiEndPointUrl . '/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());

        $headers = [
            'HTTP_api-key' => 'fake_api_key',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1002 - Request Time header is missing');


        $headers = [
            'HTTP_api-key'          => 'fake_api_key',
            'HTTP_api-request-time' => 'fake_time',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1002 - Request Time header is missing');

        $headers = [
            'HTTP_api-key'          => 'fake_api_key',
            'HTTP_api-request-time' => '0',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1002 - Request Time header is missing');

        $headers = [
            'HTTP_api-key'          => 'fake_api_key',
            'HTTP_api-request-time' => time()-3600,
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1003 - Request Hash header is missing');

        $headers = [
            'HTTP_api-key'          => 'fake_api_key',
            'HTTP_api-request-time' => time()-3600,
            'HTTP_api-request-hash' => 'fake_hash',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1004 - API Key header is invalid');

        $headers = [
            'HTTP_api-key'          => $apiKey,
            'HTTP_api-request-time' => time()-3600,
            'HTTP_api-request-hash' => 'fake_hash',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1005 - Request Time header is invalid');

        $headers = [
            'HTTP_api-key'          => $apiKey,
            'HTTP_api-request-time' => time(),
            'HTTP_api-request-hash' => 'fake_hash',
        ];
        $client->request('GET', $this->apiEndPointUrl . '/', [], [], $headers);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1006 - Request Hash header is invalid');

        $this->callApiPartner($client, 'GET', '/');
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1007 - Asked route is unknown');

        $this->callApiPartner($client, 'POST', '/test/123', ['name' => 'test']);
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1008 - Asked route is not allowed');

        $this->callApiPartner($client, 'GET', '/hello_world');
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 2000 - name - parameter is required');

        $this->setPartnerConfigurationCredentials(false, $apiKey, $apiSecret);

        $this->callApiPartner($client, 'GET', '/');
        $this->assertApiPartnerSecurityFailed($client, 'ERROR 1004 - Partner API is not enabled');
    }

    protected function setPartnerConfigurationCredentials(bool $enabled, string $apiKey, string $apiSecret): void
    {
        $configurationManager = $this->getContainer()->get(ConfigurationManager::class);
        $configurationManager->set('api.partner.enabled', $enabled ? 1 : 0);
        $configurationManager->set('api.partner.api_key', $apiKey);
        $configurationManager->setEncrypted('api.partner.api_secret', $apiSecret);
    }
}
