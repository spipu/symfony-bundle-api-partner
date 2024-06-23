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

use Spipu\ApiPartnerBundle\Model\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestService
{
    private RequestSecurityServiceInterface $requestSecurityService;

    public function __construct(RequestSecurityServiceInterface $requestSecurityService)
    {
        $this->requestSecurityService = $requestSecurityService;
    }

    public function buildRequest(Request $request, string $routeUrl, SymfonyRequest $symfonyRequest): void
    {
        $request->setUserAgent($symfonyRequest->headers->get('User-Agent', ''));
        $request->setUserIp($symfonyRequest->getClientIp());
        $request->setRoute($routeUrl);
        $request->setMethod($symfonyRequest->getMethod());
        $request->setQueryString($symfonyRequest->server->get('QUERY_STRING', ''));
        $request->setQueryArray($symfonyRequest->query->all());
        $request->setBodyString($this->getBodyString($symfonyRequest));

        $this->requestSecurityService->validate($request, $symfonyRequest);
    }

    private function getBodyString(SymfonyRequest $symfonyRequest): string
    {
        $bodyString = $symfonyRequest->getContent();
        if ($bodyString === false) {
            $bodyString = '';
        }

        return $bodyString;
    }
}
