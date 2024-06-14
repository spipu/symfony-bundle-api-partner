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

    public function __construct(iterable $routes)
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    private function addRoute(RouteInterface $route): void
    {
        $this->routes[$route->getCode()] = $route;
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
        return array_keys($this->routes);
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
