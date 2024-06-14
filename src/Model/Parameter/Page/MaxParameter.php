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

class MaxParameter extends IntegerParameter
{
    public function __construct()
    {
        $this
            ->setRequired(false)
            ->setMinValue(1)
            ->setMaxValue(1000)
            ->setDefaultValue(10)
            ->setDescription("this resource supports offset pagination - Pagination size")
        ;
    }
}
