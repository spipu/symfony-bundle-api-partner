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

namespace Spipu\ApiPartnerBundle\Model;

use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Entity\PartnerInterface;
use Spipu\ApiPartnerBundle\Exception\RouteException;

class Context
{
    private ?RouteInterface $route = null;
    private array $pathParameters = [];
    private array $queryParameters = [];
    private array $bodyParameters = [];
    private ?PartnerInterface $partner = null;

    public function getRoute(): ?RouteInterface
    {
        return $this->route;
    }

    public function setRoute(RouteInterface $route): void
    {
        $this->route = $route;
    }

    public function setPathParameters(array $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    public function setQueryParameters(array $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    public function setBodyParameters(array $bodyParameters): void
    {
        $this->bodyParameters = $bodyParameters;
    }

    /**
     * @return mixed
     */
    public function getPathParameter(string $key)
    {
        if (!array_key_exists($key, $this->pathParameters)) {
            throw new RouteException('This path parameter does not exist for this route');
        }

        return $this->pathParameters[$key];
    }

    /**
     * @return mixed
     */
    public function getQueryParameter(string $key)
    {
        if (!array_key_exists($key, $this->queryParameters)) {
            throw new RouteException('This query parameter does not exist for this route');
        }

        return $this->queryParameters[$key];
    }

    /**
     * @return mixed
     */
    public function getBodyParameter(string $key)
    {
        if (!array_key_exists($key, $this->bodyParameters)) {
            throw new RouteException('This body parameter does not exist for this route');
        }

        return $this->bodyParameters[$key];
    }

    public function getPartner(): ?PartnerInterface
    {
        return $this->partner;
    }

    public function setPartner(?PartnerInterface $partner): void
    {
        $this->partner = $partner;
    }
}
