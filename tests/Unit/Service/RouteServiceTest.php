<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Service\RouteService;
use Spipu\ApiPartnerBundle\Service\SecretParameterService;

class RouteServiceTest extends TestCase
{
    public function testSecretInBodyIsAllowed(): void
    {
        $route = $this->getRoute([], [], ['password' => (new StringParameter())->setSecret(true)]);

        $service = new RouteService([$route], new SecretParameterService());

        $this->assertSame(['test_route' => $route], $service->getAvailableRoutes());
        $this->assertSame($route, $service->get('test_route'));
    }

    public function testSecretInPathIsForbidden(): void
    {
        $route = $this->getRoute(['password' => (new StringParameter())->setSecret(true)], [], []);

        $this->expectException(RouteException::class);
        $this->expectExceptionMessage(
            'Route [test_route] - secret parameters are not allowed in path parameters, use body parameters'
        );

        new RouteService([$route], new SecretParameterService());
    }

    public function testSecretInQueryIsForbidden(): void
    {
        $route = $this->getRoute(
            [],
            [
                'user' => (new ObjectParameter())
                    ->addProperty('password', (new StringParameter())->setSecret(true))
            ],
            []
        );

        $this->expectException(RouteException::class);
        $this->expectExceptionMessage(
            'Route [test_route] - secret parameters are not allowed in query parameters, use body parameters'
        );

        new RouteService([$route], new SecretParameterService());
    }

    private function getRoute(array $pathParameters, array $queryParameters, array $bodyParameters): RouteInterface
    {
        $route = $this->createMock(RouteInterface::class);
        $route->method('getCode')->willReturn('test_route');
        $route->method('getPathParameters')->willReturn($pathParameters);
        $route->method('getQueryParameters')->willReturn($queryParameters);
        $route->method('getBodyParameters')->willReturn($bodyParameters);

        return $route;
    }
}
