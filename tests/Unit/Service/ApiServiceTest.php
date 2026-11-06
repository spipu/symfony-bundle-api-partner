<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use Exception;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
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

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ApiService::class)]
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

    public function testGenericExceptionTraceHasNoArguments(): void
    {
        $loggedContent = null;
        $loggerService = $this->createMock(LoggerServiceInterface::class);
        $loggerService
            ->expects($this->once())
            ->method('createLog')
            ->willReturnCallback(
                function ($request, $context, Response $response) use (&$loggedContent) {
                    $loggedContent = $response->getContent();
                    return null;
                }
            );

        $previousIgnoreArgs = ini_set('zend.exception_ignore_args', '0');
        $previousMaxLength = ini_set('zend.exception_string_param_max_len', '15');
        try {
            $exception = $this->createCustomerException('john@example.com');
            $this->assertStringContainsString('john@', $exception->getTraceAsString());

            $action = $this->createStub(ActionInterface::class);
            $action->method('execute')->willThrowException($exception);

            $response = $this->createService($action, $loggerService)->execute('/mock', new SymfonyRequest());
        } finally {
            ini_set('zend.exception_ignore_args', (string) $previousIgnoreArgs);
            ini_set('zend.exception_string_param_max_len', (string) $previousMaxLength);
        }

        $this->assertSame(500, $response->getCode());
        $this->assertStringStartsWith("ERROR - Invalid customer email length 16\n#0 ", $response->getContent());
        $this->assertStringContainsString(self::class . '->createCustomerException()', $response->getContent());
        $this->assertStringEndsWith(' {main}', $response->getContent());
        $this->assertStringNotContainsString('john@', $response->getContent());
        $this->assertSame($response->getContent(), $loggedContent);
    }

    private function createCustomerException(string $email): Exception
    {
        return new Exception(sprintf('Invalid customer email length %d', strlen($email)));
    }

    private function executeAndGetResponseFormatError(Response $actionResponse): ?string
    {
        $action = $this->createStub(ActionInterface::class);
        $action->method('execute')->willReturn($actionResponse);

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

        $service = $this->createService($action, $loggerService);

        $this->assertSame($actionResponse, $service->execute('/mock', new SymfonyRequest()));

        return $responseFormatError;
    }

    private function createService(ActionInterface $action, LoggerServiceInterface $loggerService): ApiService
    {
        $route = $this->createStub(RouteInterface::class);
        $route->method('getActionServiceName')->willReturn('mock_action');
        $route->method('getResponseFormat')->willReturn(new ResponseFormat('json'));

        $routeService = $this->createStub(RouteService::class);
        $routeService->method('identifyRoute')->willReturn($route);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')->willReturn($action);

        $requestSecurityService = $this->createStub(RequestSecurityServiceInterface::class);
        $requestSecurityService->method('isRouteAllowed')->willReturn(true);

        $configurationManager = $this->createStub(ConfigurationManager::class);
        $configurationManager->method('get')->willReturn(true);

        return new ApiService(
            $this->createStub(RequestService::class),
            new ContextService(),
            $routeService,
            $loggerService,
            $container,
            $configurationManager,
            $this->createStub(EnvironmentInterface::class),
            $requestSecurityService
        );
    }
}
