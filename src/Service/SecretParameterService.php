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

use Spipu\ApiPartnerBundle\Model\Parameter\ArrayParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class SecretParameterService
{
    public const SECRET_VALUE = '###SECRET###';

    /**
     * @param ParameterInterface[] $parameters
     */
    public function hasSecret(array $parameters): bool
    {
        foreach ($parameters as $parameter) {
            if ($this->isSecretParameter($parameter)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $json
     * @param ParameterInterface[] $parameters
     * @return string
     */
    public function maskJson(string $json, array $parameters): string
    {
        if (!$this->hasSecret($parameters)) {
            return $json;
        }

        $values = json_decode($json, true);
        if (!is_array($values)) {
            return self::SECRET_VALUE;
        }

        return (string) json_encode($this->maskValues($values, $parameters));
    }

    /**
     * @param array $values
     * @param ParameterInterface[] $parameters
     * @return array
     */
    private function maskValues(array $values, array $parameters): array
    {
        foreach ($parameters as $key => $parameter) {
            if (array_key_exists($key, $values)) {
                $values[$key] = $this->maskValue($values[$key], $parameter);
            }
        }

        return $values;
    }

    private function maskValue(mixed $value, ParameterInterface $parameter): mixed
    {
        if ($parameter->isSecret()) {
            return self::SECRET_VALUE;
        }

        if (!is_array($value)) {
            return $value;
        }

        if ($parameter instanceof ObjectParameter) {
            return $this->maskValues($value, $parameter->getProperties());
        }

        if ($parameter instanceof ArrayParameter && $parameter->getItemParameter() !== null) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->maskValue($item, $parameter->getItemParameter());
            }
        }

        return $value;
    }

    private function isSecretParameter(ParameterInterface $parameter): bool
    {
        if ($parameter->isSecret()) {
            return true;
        }

        if ($parameter instanceof ObjectParameter) {
            return $this->hasSecret($parameter->getProperties());
        }

        if ($parameter instanceof ArrayParameter && $parameter->getItemParameter() !== null) {
            return $this->isSecretParameter($parameter->getItemParameter());
        }

        return false;
    }
}
