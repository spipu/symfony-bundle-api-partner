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

use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\UiBundle\Entity\EntityInterface;

interface LoggerServiceInterface
{
    public function createLog(
        Request $request,
        Context $context,
        Response $response,
        float $duration,
        ?string $responseFormatError
    ): ?EntityInterface;
}
