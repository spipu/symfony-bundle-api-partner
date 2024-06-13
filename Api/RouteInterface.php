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

namespace Spipu\ApiPartnerBundle\Api;

use Spipu\ApiPartnerBundle\Model\ParameterInterface;
use Spipu\ApiPartnerBundle\Model\ResponseFormat;

interface RouteInterface
{
    public function getCode(): string;

    public function isDeprecated(): bool;

    public function getDescription(): ?string;

    public function getRoutePattern(): string;

    public function getHttpMethod(): string;

    /**
     * @return ParameterInterface[]
     */
    public function getPathParameters(): array;

    /**
     * @return ParameterInterface[]
     */
    public function getQueryParameters(): array;

    /**
     * @return ParameterInterface[]
     */
    public function getBodyParameters(): array;

    public function getActionServiceName(): string;

    public function getResponseFormat(): ?ResponseFormat;
}
