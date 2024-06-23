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
use Spipu\ApiPartnerBundle\Model\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface RequestSecurityServiceInterface
{
    public function validate(Request $request, SymfonyRequest $symfonyRequest): void;

    public function isRouteAllowed(RouteInterface $route, ?PartnerInterface $partner): bool;
}
