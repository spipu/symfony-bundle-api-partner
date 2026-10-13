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

use DateInterval;
use DateTimeImmutable;
use Spipu\ApiPartnerBundle\Repository\ApiLogPartnerRepository;
use Spipu\ConfigurationBundle\Service\ConfigurationManager;

class LogCleaner
{
    private ConfigurationManager $configurationManager;
    private ApiLogPartnerRepository $apiLogPartnerRepository;

    public function __construct(
        ConfigurationManager $configurationManager,
        ApiLogPartnerRepository $apiLogPartnerRepository
    ) {
        $this->configurationManager = $configurationManager;
        $this->apiLogPartnerRepository = $apiLogPartnerRepository;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->configurationManager->get('api.partner.cleanup.logs');
    }

    public function getKeepDays(): int
    {
        return max(1, (int) $this->configurationManager->get('api.partner.cleanup.logs_after'));
    }

    public function getLimitDate(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->sub(new DateInterval('P' . $this->getKeepDays() . 'D'));
    }

    public function cleanLogs(): int
    {
        return $this->apiLogPartnerRepository->deleteOlderThan($this->getLimitDate());
    }
}
