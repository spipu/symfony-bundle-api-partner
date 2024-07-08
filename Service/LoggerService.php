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

use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ConfigurationBundle\Service\ConfigurationManager;

class LoggerService implements LoggerServiceInterface
{
    private ConfigurationManager $configurationManager;
    private LogBuilderFactory $logBuilderFactory;

    public function __construct(
        ConfigurationManager $configurationManager,
        LogBuilderFactory $logBuilderFactory
    ) {
        $this->configurationManager = $configurationManager;
        $this->logBuilderFactory = $logBuilderFactory;
    }

    public function createLog(
        Request $request,
        Context $context,
        Response $response,
        float $duration,
        ?string $responseFormatError
    ): ?ApiLogPartner {
        if ($request->getApiKey() === '') {
            return null;
        }

        if (!$this->needLog($response, $duration, $responseFormatError)) {
            return null;
        }

        $builder = $this->logBuilderFactory->create();

        $builder->addRequest($request);
        if ($context->getRoute()) {
            $builder->addRouteCode($context->getRoute()->getCode());
        }
        $builder->addResponse($response);
        $builder->addDuration($duration);
        if ($responseFormatError) {
            $builder->addResponseFormatError($responseFormatError);
        }

        return $builder->create();
    }

    private function needLog(
        Response $response,
        float $duration,
        ?string $responseFormatError
    ): bool {
        if (
            $response->getCode() === 200
            && !$this->isLogDebugEnabled()
            && empty($responseFormatError)
            && ($duration < $this->getLogSlowQuery())
        ) {
            return false;
        }

        return true;
    }

    private function isLogDebugEnabled(): bool
    {
        return (bool) $this->configurationManager->get('api.partner.log_debug');
    }

    private function getLogSlowQuery(): float
    {
        return (float) $this->configurationManager->get('api.partner.log_slow_query');
    }
}
