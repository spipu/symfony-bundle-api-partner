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

class ObjectParameter extends AbstractParameter
{
    private array $properties = [];

    public function getCode(): string
    {
        return 'object';
    }

    public function getName(): string
    {
        return 'Object';
    }

    public function addProperty(string $key, ParameterInterface $parameter): self
    {
        $this->properties[$key] = $parameter;
        return $this;
    }

    /**
     * @return ParameterInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    protected function getDefaultValue(): ?string
    {
        return null;
    }

    protected function validateValueType(string $key, mixed $value): array
    {
        if (!is_array($value)) {
            throw $this->createException($key, 'parameter must be an object');
        }

        $count = count($value);
        if ($count > 0 && array_keys($value) === range(0, $count - 1)) {
            throw $this->createException($key, 'parameter must be an object');
        }

        foreach ($this->properties as $propertyKey => $propertyAttribute) {
            $propertyValue = null;
            if (array_key_exists($propertyKey, $value)) {
                $propertyValue = $value[$propertyKey];
            }

            $value[$propertyKey] = $propertyAttribute->validateValue($key . '.' . $propertyKey, $propertyValue);
        }

        return $value;
    }
}
