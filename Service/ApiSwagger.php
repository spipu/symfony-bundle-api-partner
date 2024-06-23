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
use Spipu\ApiPartnerBundle\Entity\PartnerInterface;
use Throwable;

class ApiSwagger
{
    private RouteService $routeService;
    private RequestSecurityServiceInterface $requestSecurityService;

    public function __construct(
        RouteService $routeService,
        RequestSecurityServiceInterface $requestSecurityService
    ) {
        $this->routeService = $routeService;
        $this->requestSecurityService = $requestSecurityService;
    }

    public function getGroupedRouteCodes(?PartnerInterface $partner = null): array
    {
        $routes = $this->routeService->getAvailableRoutes();
        ksort($routes);

        $list = [];
        foreach ($routes as $route) {
            if (!$this->requestSecurityService->isRouteAllowed($route, $partner)) {
                continue;
            }
            $routeCode = $route->getCode();
            $parts = explode('-', $routeCode, 2);
            if (count($parts) === 1) {
                $parts = ['Api', $routeCode];
            }
            $list[$parts[0]][] = $routeCode;
        }

        return $list;
    }

    public function getRoute(?string $routeCode, ?PartnerInterface $partner = null): ?RouteInterface
    {
        if ($routeCode === null) {
            return null;
        }

        try {
            $route = $this->routeService->get($routeCode);
        } catch (Throwable $e) {
            return null;
        }

        if (!$this->requestSecurityService->isRouteAllowed($route, $partner)) {
            return null;
        }

        return $route;
    }
}
