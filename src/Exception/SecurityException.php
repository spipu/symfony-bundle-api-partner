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

class SecurityException extends ApiException
{
    public const ERROR_MISSING_API_KEY      = 1001;
    public const ERROR_MISSING_REQUEST_TIME = 1002;
    public const ERROR_MISSING_REQUEST_HASH = 1003;
    public const ERROR_INVALID_API_KEY      = 1004;
    public const ERROR_INVALID_REQUEST_TIME = 1005;
    public const ERROR_INVALID_REQUEST_HASH = 1006;
    public const ERROR_UNKNOWN_ROUTE        = 1007;
    public const ERROR_NOT_ALLOWED_ROUTE    = 1008;
}
