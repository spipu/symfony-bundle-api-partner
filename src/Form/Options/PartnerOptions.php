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

namespace Spipu\ApiPartnerBundle\Form\Options;

use Spipu\ApiPartnerBundle\Entity\PartnerInterface;
use Spipu\ApiPartnerBundle\Repository\PartnerRepositoryInterface;
use Spipu\UiBundle\Form\Options\AbstractOptions;

class PartnerOptions extends AbstractOptions
{
    private PartnerRepositoryInterface $partnerRepository;

    public function __construct(
        PartnerRepositoryInterface $partnerRepository
    ) {
        $this->partnerRepository = $partnerRepository;
    }

    protected function buildOptions(): array
    {
        /** @var PartnerInterface[] $partners */
        $partners = $this->partnerRepository->getAllPartners();

        $list = [];
        foreach ($partners as $partner) {
            $list[$partner->getId()] = $partner->getApiName();
        }

        asort($list);

        return $list;
    }

    public function getTranslatableType(): string
    {
        return self::TRANSLATABLE_NO;
    }
}
