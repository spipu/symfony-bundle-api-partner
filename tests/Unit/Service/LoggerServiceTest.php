<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Service\LogBuilder;
use Spipu\ApiPartnerBundle\Service\LogBuilderFactory;
use Spipu\ApiPartnerBundle\Service\LoggerService;
use Spipu\ConfigurationBundle\Tests\SpipuConfigurationMock;

class LoggerServiceTest extends TestCase
{
    private const SLOW_QUERY = 5.;

    public function testNoApiKey(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(500);

        $this->assertNull($service->createLog($this->getRequest(''), new Context(), $response, 0., null));
    }

    public function testSuccessIsNotLogged(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(200);

        $this->assertNull($service->createLog($this->getRequest(), new Context(), $response, 0., null));
    }

    public function testDefaultCodeIsNotLogged(): void
    {
        $service = $this->getService();

        $this->assertNull($service->createLog($this->getRequest(), new Context(), new Response(), 0., null));
    }

    public function testErrorIsLogged(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(500);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., null)
        );
    }

    public function testForcedLogNeededIsLogged(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(200);
        $response->setLogNeeded(true);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., null)
        );
    }

    public function testForcedNoLogOnErrorIsNotLogged(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(404);
        $response->setLogNeeded(false);

        $this->assertNull($service->createLog($this->getRequest(), new Context(), $response, 0., null));
    }

    public function testForcedNoLogIsOverwrittenBySetCode(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setLogNeeded(false);
        $response->setCode(404);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., null)
        );
    }

    public function testForcedNoLogIsIgnoredWhenDebugEnabled(): void
    {
        $service = $this->getService(true);

        $response = new Response();
        $response->setCode(404);
        $response->setLogNeeded(false);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., null)
        );
    }

    public function testSuccessIsLoggedWhenDebugEnabled(): void
    {
        $service = $this->getService(true);

        $response = new Response();
        $response->setCode(200);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., null)
        );
    }

    public function testSuccessIsLoggedWhenSlow(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(200);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, self::SLOW_QUERY + 1., null)
        );
    }

    public function testSuccessIsLoggedWhenResponseFormatError(): void
    {
        $service = $this->getService();

        $response = new Response();
        $response->setCode(200);

        $this->assertInstanceOf(
            ApiLogPartner::class,
            $service->createLog($this->getRequest(), new Context(), $response, 0., 'fake format error')
        );
    }

    private function getService(bool $logDebug = false): LoggerService
    {
        $configurationManager = SpipuConfigurationMock::getManager(
            $this,
            [
                'api.partner.log_debug' => 'boolean',
                'api.partner.log_slow_query' => 'string',
            ],
            [
                'api.partner.log_debug' => $logDebug,
                'api.partner.log_slow_query' => self::SLOW_QUERY,
            ]
        );

        $builder = $this->createMock(LogBuilder::class);
        $builder
            ->method('create')
            ->willReturn(new ApiLogPartner());

        $logBuilderFactory = $this->createMock(LogBuilderFactory::class);
        $logBuilderFactory
            ->method('create')
            ->willReturn($builder);

        return new LoggerService($configurationManager, $logBuilderFactory);
    }

    private function getRequest(string $apiKey = 'fake-api-key'): Request
    {
        $request = new Request();
        $request->setApiKey($apiKey);

        return $request;
    }
}
