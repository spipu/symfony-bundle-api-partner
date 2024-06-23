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

use Exception;
use Spipu\ApiPartnerBundle\Api\ActionInterface;
use Spipu\ApiPartnerBundle\Exception\ApiException;
use Spipu\ApiPartnerBundle\Exception\ResponseException;
use Spipu\ApiPartnerBundle\Exception\SecurityException;
use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ConfigurationBundle\Service\ConfigurationManager;
use Spipu\CoreBundle\Service\EnvironmentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class ApiService
{
    private RequestService $requestService;
    private ContextService $contextService;
    private RouteService $routeService;
    private Request $lastRequest;
    private Context $lastContext;
    private LogBuilderFactory $logBuilderFactory;
    private ContainerInterface $container;
    private ConfigurationManager $configurationManager;
    private EnvironmentInterface $environment;
    private RequestSecurityServiceInterface $requestSecurityService;

    public function __construct(
        RequestService $requestService,
        ContextService $contextService,
        RouteService $routeService,
        LogBuilderFactory $logBuilderFactory,
        ContainerInterface $container,
        ConfigurationManager $configurationManager,
        EnvironmentInterface $environment,
        RequestSecurityServiceInterface $requestSecurityService
    ) {
        $this->requestService = $requestService;
        $this->contextService = $contextService;
        $this->routeService = $routeService;
        $this->logBuilderFactory = $logBuilderFactory;
        $this->container = $container;
        $this->configurationManager = $configurationManager;
        $this->environment = $environment;
        $this->requestSecurityService = $requestSecurityService;
    }

    public function execute(string $routeUrl, SymfonyRequest $symfonyRequest): Response
    {
        $startTime = (float) microtime(true);
        $hideError = false;
        $responseFormatError = null;
        try {
            $response = $this->prepareAndExecute($routeUrl, $symfonyRequest);
            $responseFormat = $this->lastContext->getRoute()->getResponseFormat();
            if ($responseFormat && $this->mustValidateResponseFormat()) {
                $this->contextService->validateResponseFormat($responseFormat, $response);
            }
        } catch (ResponseException $e) {
            $responseFormatError = $e->getMessage();
        } catch (ApiException $e) {
            $response = new Response();
            $response
                ->setCode(500)
                ->setContentText('ERROR ' . $e->getCode() . ' - ' . $e->getMessage());
        } catch (Exception $e) {
            $hideError = $this->environment->isProduction();
            $response = new Response();
            $response
                ->setCode(500)
                ->setContentText('ERROR - ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        $deltaTime = (float) microtime(true) - $startTime;

        $this->createLog($response, $deltaTime, $responseFormatError);

        if ($hideError) {
            $response->setContentText('Internal Server Error');
        }

        return $response;
    }

    private function createLog(Response $response, float $duration, ?string $responseFormatError): void
    {
        if ($this->getLastRequest()->getApiKey() === '') {
            return;
        }

        if ($response->getCode() === 200 && !$this->isLogDebugEnabled() && $responseFormatError === null) {
            return;
        }

        try {
            $builder = $this->logBuilderFactory->create();

            $builder->addRequest($this->getLastRequest());
            if ($this->getLastContext()->getRoute()) {
                $builder->addRouteCode(
                    $this->getLastContext()->getRoute()->getCode()
                );
            }
            $builder->addResponse($response);
            $builder->addDuration($duration);
            if ($responseFormatError) {
                $builder->addResponseFormatError($responseFormatError);
            }

            $log = $builder->create();
            $response->setLogId($log->getId());
        } catch (Exception $e) {
            $response->setLogError($e->getMessage());
        }
    }

    private function prepareAndExecute(string $routeUrl, SymfonyRequest $symfonyRequest): Response
    {
        $this->lastRequest = new Request();
        $this->lastContext = new Context();

        $this->requestService->buildRequest($this->lastRequest, $routeUrl, $symfonyRequest);

        $route = $this->routeService->identifyRoute($this->lastRequest);

        $this->lastContext->setRoute($route);
        $this->lastContext->setPartner($this->lastRequest->getPartner());

        if (!$this->requestSecurityService->isRouteAllowed($route, $this->lastContext->getPartner())) {
            throw new SecurityException('Asked route is not allowed', SecurityException::ERROR_NOT_ALLOWED_ROUTE);
        }

        $this->contextService->buildContext($this->lastContext, $this->lastRequest);

        return $this->executeAction();
    }

    private function executeAction(): Response
    {
        $route = $this->lastContext->getRoute();
        $executeServiceName = $route->getActionServiceName();
        try {
            $executeService = $this->container->get($executeServiceName);
        } catch (Exception $e) {
            throw new ApiException($route->getCode() . ' - The linked action does not exists');
        }

        if (!($executeService instanceof ActionInterface)) {
            throw new ApiException($route->getCode() . ' - The linked action must implement ActionInterface');
        }

        return $executeService->execute($this->lastContext);
    }

    public function getLastRequest(): Request
    {
        return $this->lastRequest;
    }

    public function getLastContext(): Context
    {
        return $this->lastContext;
    }

    public function isLogDebugEnabled(): bool
    {
        return (bool) $this->configurationManager->get('api.partner.log_debug');
    }

    public function mustValidateResponseFormat(): bool
    {
        return (bool) $this->configurationManager->get('api.partner.validate_response_format');
    }
}
