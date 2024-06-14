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

use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class FileParameter extends ObjectParameter
{
    public function __construct(array $allowedExtensions)
    {
        $filenameParameter = new StringParameter();
        $filenameParameter->setRequired(true);

        if (count($allowedExtensions) === 0) {
            throw new RouteException('Type of document undefined', 3000);
        }
        $filenameParameter->setPattern('/\.(' . implode('|', $allowedExtensions) . ')$/');

        $contentParameter = new StringParameter();
        $contentParameter->setRequired(true);
        $contentParameter->setDescription('The content of the file must be encoded in Base64');

        $this
            ->addProperty('filename', $filenameParameter)
            ->addProperty('content', $contentParameter);
    }
}
