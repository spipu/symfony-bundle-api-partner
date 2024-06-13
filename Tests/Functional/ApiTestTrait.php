<?php

namespace Spipu\ApiPartnerBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ApiTestTrait
{
    private string $apiEndPoint;
    private string $apiKey;
    private string $apiSecret;

    protected function setPartnerProfile(
        string $apiEndPoint,
        string $apiKey,
        string $apiSecret
    ): void {
        $this->apiEndPoint = $apiEndPoint;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    protected function callApiPartner(
        KernelBrowser $client,
        string $method,
        string $uri,
        array $queryParams = [],
        string $requestBody = ''
    ) {
        $fullUri = $this->buildPartnerFullUri($uri, $queryParams);
        $headers = $this->buildPartnerHeaders($method, $fullUri, $requestBody);

        if ($requestBody === '') {
            $requestBody = null;
        }

        $client->request($method, $this->apiEndPoint . $fullUri, [], [], $headers, $requestBody);
    }

    private function buildPartnerFullUri(string $uri, array $queryParams): string
    {
        $builtQueryParams = http_build_query($queryParams);

        $fullUri = $uri;
        if ($builtQueryParams) {
            $fullUri .= '?' . $builtQueryParams;
        }

        return $fullUri;
    }

    private function buildPartnerHeaders(string $method, string $fullUri, string $requestBody): array
    {
        $requestTime = time();

        $hashParts = [
            $this->apiKey,
            $requestTime,
            $this->apiSecret
        ];

        $requestHash = hash('sha256', implode('', $hashParts));

        return [
            'HTTP_api-key' => $this->apiKey,
            'HTTP_api-request-time' => $requestTime,
            'HTTP_api-request-hash' => $requestHash,
        ];
    }
    protected function assertApiPartnerSuccessText(KernelBrowser $client, int $status = 200): string
    {
        return $this->assertApiPartnerSuccess($client, $status, 'application/text');
    }

    protected function assertApiPartnerSuccessCsv(KernelBrowser $client, int $status = 200, string $separator = ';'): array
    {
        $csvString = $this->assertApiPartnerSuccess($client, $status, 'application/csv');

        $csvArray = [];
        $csvLines = explode("\n", $csvString);
        foreach ($csvLines as $csvLine) {
            $csvArray[] = str_getcsv($csvLine, $separator);
        }

        return $csvArray;
    }

    protected function assertApiPartnerSuccessJson(KernelBrowser $client, int $status = 200): array
    {
        $jsonString = $this->assertApiPartnerSuccess($client, $status, 'application/json');
        $this->assertNotEmpty($jsonString);
        $jsonContent = json_decode($jsonString, true);
        $this->assertIsArray($jsonContent);

        return (array) $jsonContent;
    }

    protected function assertApiPartnerSuccessPdf(KernelBrowser $client, int $status = 200): string
    {
        return $this->assertApiPartnerSuccess($client, $status, 'application/pdf');
    }

    protected function assertApiPartnerSuccessJpg(KernelBrowser $client, int $status = 200): string
    {
        return $this->assertApiPartnerSuccess($client, $status, 'image/jpg');
    }

    protected function assertApiPartnerSuccess(
        KernelBrowser $client,
        int $status,
        string $contentType
    ): string {
        if ($client->getResponse()->getStatusCode() !== $status) {
            echo "====[DEBUG][START]====";
            print_r($client->getResponse());
            echo "====[DEBUG][END]====";
        }

        $this->assertSame($status, $client->getResponse()->getStatusCode());
        $this->assertSame($contentType, $client->getResponse()->headers->get('content-type'));

        return (string) $client->getResponse()->getContent();
    }

    protected function assertApiPartnerSecurityFailed(KernelBrowser $client, string $errorMessage)
    {
        $this->assertSame(500, $client->getResponse()->getStatusCode());
        $this->assertSame($errorMessage, $client->getResponse()->getContent());
    }
}
