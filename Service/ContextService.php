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
use Spipu\ApiPartnerBundle\Exception\ResponseException;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Model\ResponseFormat;
use Throwable;

class ContextService
{
    public function buildContext(Context $context, Request $request, RouteInterface $route): void
    {
        $context->setRoute($route);
        $context->setPartner($request->getPartner());

        $this->populatePathParameters($context, $request);
        $this->populateQueryParameters($context, $request);
        $this->populateBodyParameters($context, $request);
    }

    public function validateResponseFormat(ResponseFormat $responseFormat, Response $response): void
    {
        $this->validateResponseFormatType($responseFormat, $response);
        if ($responseFormat->getType() === 'json') {
            $this->validateResponseFormatJson($responseFormat, $response);
        }
    }

    private function validateResponseFormatType(ResponseFormat $responseFormat, Response $response): void
    {
        if (
            $responseFormat->getContentType() !== $response->getContentType()
            || $responseFormat->isBinaryContent() !== $response->isBinaryContent()
        ) {
            throw new ResponseException(
                sprintf(
                    'Expected Response type: %s. Provided Response type: %s',
                    $responseFormat->getContentType() . ($responseFormat->isBinaryContent() ? ' (binary)' : ''),
                    $response->getContentType() . ($response->isBinaryContent() ? ' (binary)' : '')
                )
            );
        }
    }

    /**
     * @SuppressWarnings(PMD.ErrorControlOperator)
     */
    private function validateResponseFormatJson(ResponseFormat $responseFormat, Response $response): void
    {
        try {
            $values = @json_decode($response->getContent(), true);
        } catch (Throwable $e) {
            throw new ResponseException('Invalid JSON - '  . $e->getMessage());
        }

        if (!is_array($values)) {
            throw new ResponseException('JSON encoded is not an array');
        }

        try {
            $parameters = $responseFormat->getJsonContent();
            foreach ($parameters as $key => $parameter) {
                $this->validateParameter($key, $parameter, $values);
            }
        } catch (RouteException $exception) {
            throw new ResponseException($exception->getMessage());
        }
    }

    private function populatePathParameters(Context $context, Request $request): void
    {
        $regexp = '|^' . $context->getRoute()->getRoutePattern() . '$|';

        if (!preg_match($regexp, $request->getRoute(), $match)) {
            throw new RouteException('The Request route does not match');
        }

        $values = [];
        foreach ($context->getRoute()->getPathParameters() as $key => $parameter) {
            $parameter->setRequired(true);
            $values[$key] = $this->validateParameter($key, $parameter, $match);
        }
        $context->setPathParameters($values);
    }

    private function populateQueryParameters(Context $context, Request $request): void
    {
        $values = [];
        foreach ($context->getRoute()->getQueryParameters() as $key => $parameter) {
            $values[$key] = $this->validateParameter($key, $parameter, $request->getQueryArray());
        }
        $context->setQueryParameters($values);
    }

    private function populateBodyParameters(Context $context, Request $request): void
    {
        $values = [];
        foreach ($context->getRoute()->getBodyParameters() as $key => $parameter) {
            $values[$key] = $this->validateParameter($key, $parameter, $request->getBodyArray());
        }
        $context->setBodyParameters($values);
    }

    private function validateParameter(string $key, ParameterInterface $parameter, array $values)
    {
        $value = null;
        if (array_key_exists($key, $values)) {
            $value = $values[$key];
        }

        return $parameter->validateValue($key, $value);
    }
}
