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

namespace Spipu\ApiPartnerBundle\Model;

interface ParameterInterface
{
    public function getCode(): string;

    public function getName(): string;

    public function setDescription(string $description): self;

    public function getDescription(): ?string;

    public function setRequired(bool $required): ParameterInterface;

    public function isRequired(): bool;

    public function validateValue(string $key, $value);
}
