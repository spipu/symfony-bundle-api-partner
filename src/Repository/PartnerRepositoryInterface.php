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

namespace Spipu\ApiPartnerBundle\Repository;

use Spipu\ApiPartnerBundle\Entity\PartnerInterface;

interface PartnerRepositoryInterface
{
    /**
     * @return PartnerInterface[]
     */
    public function getAllPartners(): array;

    public function getPartnerByApiKey(string $apiKey): ?PartnerInterface;

    public function getPartnerById(int $id): ?PartnerInterface;
}
