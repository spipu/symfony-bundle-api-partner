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

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiControllerService
{
    private ApiService $apiService;

    public function __construct(
        ApiService $apiService
    ) {

        $this->apiService = $apiService;
    }

    public function entryPointAction(
        SymfonyRequest $symfonyRequest,
        string $routeUrl
    ): Response {
        $apiResponse = $this->apiService->execute($routeUrl, $symfonyRequest);

        if ($this->apiService->getLastRequest()->getApiKey() === '') {
            throw new NotFoundHttpException('API Key is missing');
        }

        $response = new Response();
        $response->setStatusCode($apiResponse->getCode());
        $response->setContent($apiResponse->getContent());

        $response->headers->set('Expires', '0');
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Content-Type', $apiResponse->getContentType());

        foreach ($apiResponse->getHeaders() as $headerKey => $headerValue) {
            $response->headers->set($headerKey, $headerValue);
        }

        if ($apiResponse->getLogId()) {
            $response->headers->set('Api-Log-Id', (string) $apiResponse->getLogId());
        }

        if ($apiResponse->getLogError()) {
            $errorMessage = $apiResponse->getLogError();
            $errorMessage = str_replace(["\n", "\r"], ' ', $errorMessage);
            $errorMessage = substr($errorMessage, 0, 1024);
            $response->headers->set('Api-Log-Error', $errorMessage);
        }

        return $response;
    }
}
