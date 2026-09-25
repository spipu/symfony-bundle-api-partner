<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Api\ActionInterface;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Model\ResponseFormat;
use Spipu\ApiPartnerBundle\Service\ApiService;
use Spipu\ApiPartnerBundle\Service\ContextService;
use Spipu\ApiPartnerBundle\Service\LoggerServiceInterface;
use Spipu\ApiPartnerBundle\Service\RequestSecurityServiceInterface;
use Spipu\ApiPartnerBundle\Service\RequestService;
use Spipu\ApiPartnerBundle\Service\RouteService;
use Spipu\ConfigurationBundle\Service\ConfigurationManager;
use Spipu\CoreBundle\Service\EnvironmentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ApiServiceTest extends TestCase
{
    public function testResponseFormatIsValidatedOnSuccess(): void
    {
        $response = new Response();
        $response->setContentText('not json');

        $this->assertSame(
            'Expected Response type: application/json. Provided Response type: application/text',
            $this->executeAndGetResponseFormatError($response)
        );
    }

    public function testResponseFormatIsValidOnSuccess(): void
    {
        $response = new Response();
        $response->setContentJson(['foo' => 'bar']);

        $this->assertNull($this->executeAndGetResponseFormatError($response));
    }

    public function testResponseFormatIsNotValidatedOnError(): void
    {
        $response = new Response();
        $response->setCode(404);
        $response->setContentText('Not Found');

        $this->assertNull($this->executeAndGetResponseFormatError($response));
    }

    public function testResponseFormatIsNotValidatedOnOtherSuccessCode(): void
    {
        $response = new Response();
        $response->setCode(201);
        $response->setContentText('Created');

        $this->assertNull($this->executeAndGetResponseFormatError($response));
    }

    private function executeAndGetResponseFormatError(Response $actionResponse): ?string
    {
        $route = $this->createStub(RouteInterface::class);
        $route->method('getActionServiceName')->willReturn('mock_action');
        $route->method('getResponseFormat')->willReturn(new ResponseFormat('json'));

        $routeService = $this->createStub(RouteService::class);
        $routeService->method('identifyRoute')->willReturn($route);

        $action = $this->createStub(ActionInterface::class);
        $action->method('execute')->willReturn($actionResponse);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturn($action);

        $requestSecurityService = $this->createStub(RequestSecurityServiceInterface::class);
        $requestSecurityService->method('isRouteAllowed')->willReturn(true);

        $configurationManager = $this->createStub(ConfigurationManager::class);
        $configurationManager->method('get')->willReturn(true);

        $responseFormatError = null;
        $loggerService = $this->createMock(LoggerServiceInterface::class);
        $loggerService
            ->expects($this->once())
            ->method('createLog')
            ->willReturnCallback(
                function ($request, $context, $response, $duration, ?string $error) use (&$responseFormatError) {
                    $responseFormatError = $error;
                    return null;
                }
            );

        $service = new ApiService(
            $this->createStub(RequestService::class),
            new ContextService(),
            $routeService,
            $loggerService,
            $container,
            $configurationManager,
            $this->createStub(EnvironmentInterface::class),
            $requestSecurityService
        );

        $this->assertSame($actionResponse, $service->execute('/mock', new SymfonyRequest()));

        return $responseFormatError;
    }
}
