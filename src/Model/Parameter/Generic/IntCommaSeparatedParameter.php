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

namespace Spipu\ApiPartnerBundle\Model\Parameter\Generic;

use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class IntCommaSeparatedParameter extends StringParameter
{
    public function __construct()
    {
        $this
            ->setPattern("/^(\d+)(,\s*\d+)*$/")
            ->setRequired(false)
        ;
    }
}
