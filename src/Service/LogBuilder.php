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

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;

class LogBuilder
{
    public const LOG_MAX_STRING_LENGTH = 10240;

    private EntityManagerInterface $entityManager;
    private ApiResponseStatus $apiResponseStatus;
    private SecretParameterService $secretParameterService;
    private ApiLogPartner $log;

    public function __construct(
        EntityManagerInterface $entityManager,
        ApiResponseStatus $apiResponseStatus,
        SecretParameterService $secretParameterService
    ) {
        $this->entityManager = $entityManager;
        $this->apiResponseStatus = $apiResponseStatus;
        $this->secretParameterService = $secretParameterService;
    }

    public function init(): void
    {
        $this->log = new ApiLogPartner();
        $this->log->setDate(new DateTimeImmutable());
        $this->log->setMemoryUsage(memory_get_peak_usage());
    }

    public function create(): ApiLogPartner
    {
        $this->entityManager->persist($this->log);
        $this->entityManager->flush();

        return $this->log;
    }

    public function addRequest(Request $request, ?RouteInterface $route = null): void
    {
        $bodyString = $request->getBodyString();
        if ($route !== null) {
            $bodyString = $this->secretParameterService->maskJson($bodyString, $route->getBodyParameters());
        }

        $this->log
            ->setPartnerId($request->getPartner() ? $request->getPartner()->getId() : null)
            ->setApiKey($request->getApiKey())
            ->setUserIp($request->getUserIp())
            ->setUserAgent($request->getUserAgent())
            ->setRequestTime($request->getRequestTime())
            ->setRequestHash($request->getRequestHash())
            ->setMethod($request->getMethod())
            ->setRoute($request->getRoute())
            ->setQueryString($this->limitStringLength($request->getQueryString()))
            ->setBodyString($this->limitStringLength($bodyString))
        ;
    }

    public function addRouteCode(string $routeCode): void
    {
        $this->log->setRouteCode($routeCode);
    }

    public function addResponse(Response $response, ?RouteInterface $route = null): void
    {
        $content = $response->getContent();
        if ($response->isBinaryContent()) {
            $content = '[binary content file]';
        }

        $responseFormat = $route?->getResponseFormat();
        if ($responseFormat !== null && $response->getContentType() === 'application/json') {
            $content = $this->secretParameterService->maskJson($content, $responseFormat->getJsonContent());
        }

        $this->log
            ->setResponseCode($response->getCode())
            ->setResponseStatus($this->apiResponseStatus->getStatusFromCode($response->getCode()))
            ->setResponseType($response->getContentType())
            ->setResponseContent($this->limitStringLength($content))
        ;
    }

    private function limitStringLength(string $value): string
    {
        $strLen = mb_strlen($value);
        if ($strLen > self::LOG_MAX_STRING_LENGTH) {
            return mb_substr($value, 0, self::LOG_MAX_STRING_LENGTH) . '...  (' . $strLen . ')';
        }

        return $value;
    }

    public function addDuration(float $duration): void
    {
        $this->log->setDuration($duration);
    }

    /**
     * @SuppressWarnings(PMD.ErrorControlOperator)
     */
    public function addResponseFormatError(string $responseFormatError): void
    {
        $errorMessage = "[RESPONSE FORMAT ERROR][$responseFormatError]";

        $this->log->setResponseStatus($this->apiResponseStatus::STATUS_ERROR);

        if ($this->log->getResponseType() === 'application/json') {
            $this->log->setResponseContent(json_encode([
                'error' => $errorMessage,
                'content' => (@json_decode($this->log->getResponseContent(), true) ?? $this->log->getResponseContent())
            ]));
            return;
        }

        $this->log->setResponseContent("$errorMessage\n" . $this->log->getResponseContent());
    }
}
