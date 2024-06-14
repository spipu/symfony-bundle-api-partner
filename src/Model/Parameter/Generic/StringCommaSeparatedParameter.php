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

class StringCommaSeparatedParameter extends StringParameter
{
    public function __construct()
    {
        $this
            ->setPattern("/^[0-9a-zA-Z-_]+(,[0-9a-zA-Z-_]+)*$/")
            ->setRequired(false)
        ;
    }
}
