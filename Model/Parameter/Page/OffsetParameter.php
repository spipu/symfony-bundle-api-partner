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

namespace Spipu\ApiPartnerBundle\Model\Parameter\Page;

use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;

class OffsetParameter extends IntegerParameter
{
    public function __construct()
    {
        $this
            ->setRequired(false)
            ->setMinValue(0)
            ->setDefaultValue(0)
            ->setDescription("this resource supports offset pagination - Pagination Offset")
        ;
    }
}
