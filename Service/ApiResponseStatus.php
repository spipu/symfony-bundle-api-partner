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

class ApiResponseStatus
{
    public const STATUS_SUCCESS  = 'success';
    public const STATUS_ERROR    = 'error';

    public function getStatuses(): array
    {
        return [
            static::STATUS_SUCCESS,
            static::STATUS_ERROR,
        ];
    }

    public function isError(string $status): bool
    {
        return $status === static::STATUS_ERROR;
    }

    public function getStatusFromCode(int $statusCode): string
    {
        if ($statusCode < 200 || $statusCode > 299) {
            return self::STATUS_ERROR;
        }

        return self::STATUS_SUCCESS;
    }
}
