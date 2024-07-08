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
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class ArrayParameter extends AbstractParameter
{
    private ?array $defaultValue = null;
    private ?int $minItems = null;
    private ?int $maxItems = null;
    private ?bool $exclusiveMin = null;
    private ?bool $exclusiveMax = null;
    private ?ParameterInterface $itemParameter = null;

    public function getCode(): string
    {
        return 'array';
    }

    public function getName(): string
    {
        return 'Array';
    }

    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    public function getExclusiveMin(): ?bool
    {
        return $this->exclusiveMin;
    }

    public function getExclusiveMax(): ?bool
    {
        return $this->exclusiveMax;
    }

    public function getItemParameter(): ?ParameterInterface
    {
        return $this->itemParameter;
    }

    public function setDefaultValue(?array $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param int|null $minItems
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMinItems(?int $minItems, bool $exclusive = false): self
    {
        $this->minItems = $minItems;
        $this->exclusiveMin = $exclusive;
        return $this;
    }

    /**
     * @param int|null $maxItems
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMaxItems(?int $maxItems, bool $exclusive = false): self
    {
        $this->maxItems = $maxItems;
        $this->exclusiveMax = $exclusive;
        return $this;
    }

    public function setItemParameter(?ParameterInterface $itemParameter): self
    {
        $this->itemParameter = $itemParameter;
        return $this;
    }

    public function getDefaultValue(): ?array
    {
        return $this->defaultValue;
    }

    protected function validateValueType(string $key, $value): array
    {
        if (!is_array($value)) {
            throw $this->createException($key, 'parameter must be an array');
        }

        $count = count($value);
        if ($count > 0 && array_keys($value) !== range(0, $count - 1)) {
            throw $this->createException($key, 'parameter must be an array');
        }

        $this->validateMinItems($key, $value);
        $this->validateMaxItems($key, $value);

        if ($this->itemParameter !== null) {
            foreach ($value as $itemKey => $item) {
                $value[$itemKey] = $this->itemParameter->validateValue($key . '[' . $itemKey . ']', $item);
            }
        }

        return $value;
    }

    private function validateMinItems(string $key, array $items): void
    {
        if ($this->minItems === null) {
            return;
        }

        $count = count($items);

        if ($this->exclusiveMin && $this->minItems >= $count) {
            throw $this->createException($key, 'number of items must be greater than ' . $this->minItems);
        }

        if (!$this->exclusiveMin && $this->minItems > $count) {
            throw $this->createException($key, 'number of items must be equal or greater than ' . $this->minItems);
        }
    }

    private function validateMaxItems(string $key, array $items): void
    {
        if ($this->maxItems === null) {
            return;
        }
        $count = count($items);

        if ($this->exclusiveMax && $this->maxItems <= $count) {
            throw $this->createException($key, 'number of items must be lower than ' . $this->maxItems);
        }

        if (!$this->exclusiveMax && $this->maxItems < $count) {
            throw $this->createException($key, 'number of items must be equal or lower than ' . $this->maxItems);
        }
    }
}
