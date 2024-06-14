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

namespace Spipu\ApiPartnerBundle\Exception;

use Throwable;

class RouteException extends ApiException
{
    public const DEFAULT_ERROR_CODE = 2000;

    public function __construct(string $message = "", int $code = self::DEFAULT_ERROR_CODE, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
