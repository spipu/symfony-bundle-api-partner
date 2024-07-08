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

class NumberParameter extends AbstractParameter
{
    private ?float $defaultValue = null;
    private ?float $minValue = null;
    private ?float $maxValue = null;
    private ?bool $exclusiveMin = null;
    private ?bool $exclusiveMax = null;

    public function getCode(): string
    {
        return 'number';
    }

    public function getName(): string
    {
        return 'Number';
    }

    public function getMinValue(): ?float
    {
        return $this->minValue;
    }

    public function getMaxValue(): ?float
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

    public function setDefaultValue(?float $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param float|null $minValue
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMinValue(?float $minValue, bool $exclusive = false): self
    {
        $this->minValue = $minValue;
        $this->exclusiveMin = $exclusive;
        return $this;
    }

    /**
     * @param float|null $maxValue
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMaxValue(?float $maxValue, bool $exclusive = false): self
    {
        $this->maxValue = $maxValue;
        $this->exclusiveMax = $exclusive;
        return $this;
    }

    public function getDefaultValue(): ?float
    {
        return $this->defaultValue;
    }

    protected function validateValueType(string $key, $value): float
    {
        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw $this->createException($key, 'parameter must be a number');
        }

        // Issue on PHP filter_var when testing 0 as float.
        if ($value === 0 || preg_match('/^[0]+[.]?[0]*$/', (string) $value)) {
            $value = 0.;
        }
        if ($value !== 0. && !filter_var($value, FILTER_VALIDATE_FLOAT)) {
            throw $this->createException($key, 'parameter must be a number');
        }
        $value = (float) $value;

        $this->validateMinValue($key, $value);
        $this->validateMaxValue($key, $value);

        return $value;
    }

    private function validateMinValue(string $key, float $value): void
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

    private function validateMaxValue(string $key, float $value): void
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
