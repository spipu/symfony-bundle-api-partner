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

namespace Spipu\ApiPartnerBundle\Form\Options;

use Spipu\ApiPartnerBundle\Service\ApiResponseStatus;
use Spipu\UiBundle\Form\Options\AbstractOptions;

class ApiStatusOptions extends AbstractOptions
{
    private ApiResponseStatus $apiResponseStatus;

    public function __construct(ApiResponseStatus $apiResponseStatus)
    {
        $this->apiResponseStatus = $apiResponseStatus;
    }

    protected function buildOptions(): array
    {
        $statuses = $this->apiResponseStatus->getStatuses();
        $values = [];
        foreach ($statuses as $status) {
            $values[$status] = 'spipu.api_partner.log.status.' . $status;
        }

        asort($values);

        return $values;
    }
}
