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
use Throwable;

class ApiSwagger
{
    private RouteService $routeService;

    public function __construct(
        RouteService $routeService
    ) {
        $this->routeService = $routeService;
    }

    public function getGroupedRouteCodes(): array
    {
        $routeCodes = $this->routeService->getAvailableRoutes();
        sort($routeCodes);

        $list = [];
        foreach ($routeCodes as $routeCode) {
            $parts = explode('-', $routeCode, 2);
            if (count($parts) === 1) {
                $parts = ['Api', $routeCode];
            }
            $list[$parts[0]][] = $routeCode;
        }

        return $list;
    }

    public function getRoute(?string $routeCode): ?RouteInterface
    {
        if ($routeCode === null) {
            return null;
        }

        try {
            return $this->routeService->get($routeCode);
        } catch (Throwable $e) {
            return null;
        }
    }
}
