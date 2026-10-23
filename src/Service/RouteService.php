<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Service;

use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Exception\SecurityException;
use Spipu\ApiPartnerBundle\Model\Request;

class RouteService
{
    /**
     * @var RouteInterface[]
     */
    private array $routes = [];
    private SecretParameterService $secretParameterService;

    public function __construct(iterable $routes, SecretParameterService $secretParameterService)
    {
        $this->secretParameterService = $secretParameterService;

        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    private function addRoute(RouteInterface $route): void
    {
        $this->checkNoSecret($route, 'path', $route->getPathParameters());
        $this->checkNoSecret($route, 'query', $route->getQueryParameters());

        $this->routes[$route->getCode()] = $route;
    }

    /**
     * Secret values must be sent in the body: path and query are written in the web server access logs.
     */
    private function checkNoSecret(RouteInterface $route, string $type, array $parameters): void
    {
        if ($this->secretParameterService->hasSecret($parameters)) {
            throw new RouteException(
                sprintf(
                    'Route [%s] - secret parameters are not allowed in %s parameters, use body parameters',
                    $route->getCode(),
                    $type
                )
            );
        }
    }

    public function get(string $routeCode): RouteInterface
    {
        if (!array_key_exists($routeCode, $this->routes)) {
            throw new RouteException('unknown route code');
        }

        return $this->routes[$routeCode];
    }

    public function getAvailableRoutes(): array
    {
        return $this->routes;
    }

    public function identifyRoute(Request $request): RouteInterface
    {
        foreach ($this->routes as $route) {
            if ($this->checkRoute($route, $request)) {
                return $route;
            }
        }

        throw new SecurityException(
            'Asked route is unknown',
            SecurityException::ERROR_UNKNOWN_ROUTE
        );
    }

    private function checkRoute(RouteInterface $route, Request $request): bool
    {
        if ($route->getHttpMethod() !== $request->getMethod()) {
            return false;
        }

        return (bool) preg_match('|^' . $route->getRoutePattern() . '$|', $request->getRoute());
    }
}
