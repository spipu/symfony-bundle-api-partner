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

class IntegerParameter extends AbstractParameter
{
    private ?int $defaultValue = null;
    private ?int $minValue = null;
    private ?int $maxValue = null;
    private ?bool $exclusiveMin = null;
    private ?bool $exclusiveMax = null;

    public function getCode(): string
    {
        return 'int';
    }

    public function getName(): string
    {
        return 'Integer';
    }

    public function getMinValue(): ?int
    {
        return $this->minValue;
    }

    public function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    public function getExclusiveMin(): ?bool
    {
        return $this->exclusiveMin;
    }

    public function getExclusiveMax(): ?bool
    {
        return $this->exclusiveMax;
    }

    public function setDefaultValue(?int $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param int|null $minValue
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMinValue(?int $minValue, bool $exclusive = false): self
    {
        $this->minValue = $minValue;
        $this->exclusiveMin = $exclusive;
        return $this;
    }

    /**
     * @param int|null $maxValue
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMaxValue(?int $maxValue, bool $exclusive = false): self
    {
        $this->maxValue = $maxValue;
        $this->exclusiveMax = $exclusive;
        return $this;
    }

    public function getDefaultValue(): ?int
    {
        return $this->defaultValue;
    }

    protected function validateValueType(string $key, mixed $value): int
    {
        if (!is_int($value) && (!is_string($value) || !ctype_digit((string) $value))) {
            throw $this->createException($key, 'parameter must be an integer');
        }

        $value = (int) $value;

        $this->validateMinValue($key, $value);
        $this->validateMaxValue($key, $value);

        return $value;
    }

    private function validateMinValue(string $key, int $value): void
    {
        if ($this->minValue === null) {
            return;
        }

        if ($this->exclusiveMin && $this->minValue >= $value) {
            throw $this->createException($key, 'value must be greater than ' . $this->minValue);
        }

        if (!$this->exclusiveMin && $this->minValue > $value) {
            throw $this->createException($key, 'value must be equal or greater than ' . $this->minValue);
        }
    }

    private function validateMaxValue(string $key, int $value): void
    {
        if ($this->maxValue === null) {
            return;
        }

        if ($this->exclusiveMax && $this->maxValue <= $value) {
            throw $this->createException($key, 'value must be lower than ' . $this->maxValue);
        }

        if (!$this->exclusiveMax && $this->maxValue < $value) {
            throw $this->createException($key, 'value must be equal or lower than ' . $this->maxValue);
        }
    }
}
