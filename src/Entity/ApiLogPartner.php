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

namespace Spipu\ApiPartnerBundle\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Spipu\UiBundle\Entity\EntityInterface;

/**
 * @SuppressWarnings(PMD.TooManyFields)
 */
#[ORM\Entity(repositoryClass: 'Spipu\ApiPartnerBundle\Repository\ApiLogPartnerRepository')]
#[ORM\Table(name: 'api_log_partner')]
#[ORM\Index(name: 'API_LOG_PARTNER_METHOD_INDEX', columns: ['method'])]
#[ORM\Index(name: 'API_LOG_PARTNER_ROUTE_INDEX', columns: ['route'])]
#[ORM\Index(name: 'API_LOG_PARTNER_RESPONSE_STATUS_INDEX', columns: ['response_status'])]
class ApiLogPartner implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(nullable: true)]
    private ?int $partnerId;

    #[ORM\Column]
    private ?DateTimeImmutable $date = null;

    #[ORM\Column]
    private ?int $memoryUsage = null;

    #[ORM\Column]
    private ?float $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $userIp = null;

    #[ORM\Column(length: 255)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $apiKey;

    #[ORM\Column(nullable: true)]
    private ?int $requestTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $requestHash = null;

    #[ORM\Column(length: 32)]
    private ?string $method = null;

    #[ORM\Column(length: 255)]
    private ?string $route = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $queryString = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bodyString = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $routeCode = null;

    #[ORM\Column(length: 16)]
    private string $responseStatus = '';

    #[ORM\Column]
    private ?int $responseCode = null;

    #[ORM\Column(length: 255)]
    private ?string $responseType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $responseContent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartnerId(): ?int
    {
        return $this->partnerId;
    }

    public function setPartnerId(?int $partnerId): static
    {
        $this->partnerId = $partnerId;

        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMemoryUsage(): ?int
    {
        return $this->memoryUsage;
    }

    public function setMemoryUsage(int $memoryUsage): static
    {
        $this->memoryUsage = $memoryUsage;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): static
    {
        $this->userAgent = $this->limitStringData($userAgent);

        return $this;
    }

    public function getUserIp(): ?string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): static
    {
        $this->userIp = $this->limitStringData($userIp);

        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $this->limitStringData($apiKey);

        return $this;
    }

    public function getRequestTime(): ?int
    {
        return $this->requestTime;
    }

    public function setRequestTime(?int $requestTime): static
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    public function getRequestHash(): ?string
    {
        return $this->requestHash;
    }

    public function setRequestHash(?string $requestHash): static
    {
        $this->requestHash = $this->limitStringData($requestHash);

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $this->limitStringData($method);

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $this->limitStringData($route);

        return $this;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    public function setQueryString(?string $queryString): static
    {
        $this->queryString = $this->limitStringData($queryString, 1024 * 1024);

        return $this;
    }

    public function getBodyString(): ?string
    {
        return $this->bodyString;
    }

    public function setBodyString(?string $bodyString): static
    {
        $this->bodyString = $this->limitStringData($bodyString, 1024 * 1024);

        return $this;
    }

    public function getRouteCode(): ?string
    {
        return $this->routeCode;
    }

    public function setRouteCode(?string $routeCode): static
    {
        $this->routeCode = $this->limitStringData($routeCode);

        return $this;
    }

    public function getResponseStatus(): string
    {
        return $this->responseStatus;
    }

    public function setResponseStatus(string $responseStatus): static
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function setResponseCode(int $responseCode): static
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function setResponseType(string $responseType): static
    {
        $this->responseType = $this->limitStringData($responseType);

        return $this;
    }

    public function getResponseContent(): ?string
    {
        return $this->responseContent;
    }

    public function setResponseContent(string $responseContent): static
    {
        $this->responseContent = $this->limitStringData($responseContent, 1024 * 1024);

        return $this;
    }

    private function limitStringData(?string $value, int $size = 255): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_substr($value, 0, $size);
    }

    public function getDisplayResponseContent(): string
    {
        if (!str_contains($this->getResponseType(), 'application/json')) {
            return $this->formatBigString($this->getResponseContent());
        }

        $data = json_decode($this->getResponseContent(), true);
        if ($data === null) {
            return "[JSON DECODE ERROR]\n" . $this->formatBigString($this->getResponseContent());
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT);
    }

    private function formatBigString(?string $content): string
    {
        if ($content === null) {
            return '';
        }

        return wordwrap($content, 100);
    }
}
