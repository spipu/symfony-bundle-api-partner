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

use Spipu\ApiPartnerBundle\Entity\PartnerInterface;

class Request
{
    private string $apiKey = '';
    private int $requestTime = 0;
    private string $requestHash = '';
    private string $route = '';
    private string $method = '';
    private string $queryString = '';
    private array $queryArray = [];
    private string $bodyString = '';
    private ?array $bodyArray = [];
    private ?PartnerInterface $partner = null;
    private string $userIp = '';
    private string $userAgent = '';

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getRequestTime(): int
    {
        return $this->requestTime;
    }

    public function setRequestTime(int $requestTime): void
    {
        $this->requestTime = $requestTime;
    }

    public function getRequestHash(): string
    {
        return $this->requestHash;
    }

    public function setRequestHash(string $requestHash): void
    {
        $this->requestHash = $requestHash;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function setQueryString(string $queryString): void
    {
        $this->queryString = $queryString;
    }

    public function getQueryArray(): array
    {
        return $this->queryArray;
    }

    public function setQueryArray(array $queryArray): void
    {
        $this->queryArray = $queryArray;
    }

    public function getBodyString(): string
    {
        return $this->bodyString;
    }

    public function getBodyArray(): array
    {
        return $this->bodyArray;
    }

    public function setBodyString(string $bodyString): void
    {
        $this->bodyString = $bodyString;

        $this->bodyArray = json_decode($bodyString, true);
        if (!is_array($this->bodyArray)) {
            $this->bodyArray = [];
        }
    }

    public function getPartner(): ?PartnerInterface
    {
        return $this->partner;
    }

    public function setPartner(?PartnerInterface $partner): void
    {
        $this->partner = $partner;
    }

    public function getUserIp(): string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): void
    {
        $this->userIp = $userIp;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }
}
