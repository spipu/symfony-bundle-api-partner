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

namespace Spipu\ApiPartnerBundle\Step;

use Spipu\ApiPartnerBundle\Service\LogCleaner;
use Spipu\ProcessBundle\Entity\Process\ParametersInterface;
use Spipu\ProcessBundle\Service\LoggerInterface;
use Spipu\ProcessBundle\Step\StepInterface;

class CleanLogs implements StepInterface
{
    private LogCleaner $logCleaner;

    public function __construct(LogCleaner $logCleaner)
    {
        $this->logCleaner = $logCleaner;
    }

    public function execute(ParametersInterface $parameters, LoggerInterface $logger): int
    {
        if (!$this->logCleaner->isEnabled()) {
            $logger->debug(' - disabled in app configuration');
            return 0;
        }

        $logger->debug(sprintf(' - keep logs: [%d] day(s)', $this->logCleaner->getKeepDays()));

        $nbDeleted = $this->logCleaner->cleanLogs();

        $logger->debug(sprintf(' => Deleted logs: [%d]', $nbDeleted));

        return $nbDeleted;
    }
}
