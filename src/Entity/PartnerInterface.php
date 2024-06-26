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

namespace Spipu\ApiPartnerBundle\Entity;

use Spipu\UiBundle\Entity\EntityInterface;

interface PartnerInterface extends EntityInterface
{
    public function getApiName(): ?string;

    public function getApiKey(): ?string;

    public function getApiSecretKey(): ?string;

    public function isApiEnabled(): ?bool;
}
