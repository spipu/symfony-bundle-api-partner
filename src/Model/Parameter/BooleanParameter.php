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

namespace Spipu\ApiPartnerBundle\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\AbstractParameter;

class BooleanParameter extends AbstractParameter
{
    private ?bool $defaultValue = null;

    public function getCode(): string
    {
        return 'bool';
    }

    public function getName(): string
    {
        return 'Boolean';
    }

    public function setDefaultValue(?bool $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getDefaultValue(): ?bool
    {
        return $this->defaultValue;
    }

    protected function validateValueType(string $key, mixed $value): bool
    {
        if ($value === 'false' || $value === '0' || $value === 0) {
            $value = false;
        }

        if ($value === 'true' || $value === '1' || $value === 1) {
            $value = true;
        }

        if (!is_bool($value)) {
            throw $this->createException($key, 'parameter must be a boolean');
        }

        return $value;
    }
}
