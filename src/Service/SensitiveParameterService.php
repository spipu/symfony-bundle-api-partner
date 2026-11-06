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

use Closure;
use Spipu\ApiPartnerBundle\Model\Parameter\ArrayParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class SensitiveParameterService
{
    public const SECRET_VALUE = '###SECRET###';
    public const PRIVACY_VALUE = '###PRIVACY###';

    /**
     * @param ParameterInterface[] $parameters
     */
    public function hasSecret(array $parameters): bool
    {
        return $this->hasFlag($parameters, static fn (ParameterInterface $parameter): bool => $parameter->isSecret());
    }

    /**
     * @param ParameterInterface[] $parameters
     */
    public function hasPrivacy(array $parameters): bool
    {
        return $this->hasFlag($parameters, static fn (ParameterInterface $parameter): bool => $parameter->isPrivacy());
    }

    /**
     * @param string $json
     * @param ParameterInterface[] $parameters
     * @return string
     */
    public function maskJson(string $json, array $parameters): string
    {
        if (!$this->hasSensitive($parameters)) {
            return $json;
        }

        $values = json_decode($json, true);
        if (!is_array($values)) {
            return $this->getMaskValue($parameters);
        }

        return (string) json_encode($this->maskValues($values, $parameters));
    }

    /**
     * @param string $queryString
     * @param array $queryValues
     * @param ParameterInterface[] $parameters
     * @return string
     */
    public function maskQueryString(string $queryString, array $queryValues, array $parameters): string
    {
        if (!$this->hasSensitive($parameters)) {
            return $queryString;
        }

        $maskedQuery = http_build_query($this->maskValues($queryValues, $parameters), '', '&', PHP_QUERY_RFC3986);

        return str_replace(
            [rawurlencode(self::SECRET_VALUE), rawurlencode(self::PRIVACY_VALUE)],
            [self::SECRET_VALUE, self::PRIVACY_VALUE],
            $maskedQuery
        );
    }

    /**
     * Route patterns are free regexps: any capture-based partial masking could leak, so the whole route is masked.
     * @param string $route
     * @param ParameterInterface[] $parameters
     * @return string
     */
    public function maskRoute(string $route, array $parameters): string
    {
        if ($this->hasPrivacy($parameters)) {
            return self::PRIVACY_VALUE;
        }

        return $route;
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

    /**
     * A value not having the shape accepted by the validator of its parameter is masked whole,
     * because the sensitive descendants can not be located inside it.
     */
    private function maskValue(mixed $value, ParameterInterface $parameter): mixed
    {
        if ($parameter->isSecret()) {
            return self::SECRET_VALUE;
        }

        if ($parameter->isPrivacy()) {
            return self::PRIVACY_VALUE;
        }

        if (!$this->hasSensitive([$parameter])) {
            return $value;
        }

        if ($parameter instanceof ObjectParameter) {
            return $this->maskObjectValue($value, $parameter);
        }

        if ($parameter instanceof ArrayParameter) {
            return $this->maskArrayValue($value, $parameter);
        }

        return $value;
    }

    private function maskObjectValue(mixed $value, ObjectParameter $parameter): mixed
    {
        if (!is_array($value) || (($value !== []) && array_is_list($value))) {
            return $this->getMaskValue([$parameter]);
        }

        return $this->maskValues($value, $parameter->getProperties());
    }

    private function maskArrayValue(mixed $value, ArrayParameter $parameter): mixed
    {
        $itemParameter = $parameter->getItemParameter();
        if ($itemParameter === null) {
            return $value;
        }

        if (!is_array($value) || !array_is_list($value)) {
            return $this->getMaskValue([$parameter]);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->maskValue($item, $itemParameter);
        }

        return $value;
    }

    /**
     * @param ParameterInterface[] $parameters
     */
    private function getMaskValue(array $parameters): string
    {
        if ($this->hasSecret($parameters)) {
            return self::SECRET_VALUE;
        }

        return self::PRIVACY_VALUE;
    }

    /**
     * @param ParameterInterface[] $parameters
     */
    private function hasSensitive(array $parameters): bool
    {
        return $this->hasFlag(
            $parameters,
            static fn (ParameterInterface $parameter): bool => ($parameter->isSecret() || $parameter->isPrivacy())
        );
    }

    /**
     * @param ParameterInterface[] $parameters
     * @param Closure $isFlagged  Format: function(ParameterInterface $parameter): bool.
     * @return bool
     */
    private function hasFlag(array $parameters, Closure $isFlagged): bool
    {
        foreach ($parameters as $parameter) {
            if ($this->isFlaggedParameter($parameter, $isFlagged)) {
                return true;
            }
        }

        return false;
    }

    private function isFlaggedParameter(ParameterInterface $parameter, Closure $isFlagged): bool
    {
        if ($isFlagged($parameter)) {
            return true;
        }

        if ($parameter instanceof ObjectParameter) {
            return $this->hasFlag($parameter->getProperties(), $isFlagged);
        }

        if (($parameter instanceof ArrayParameter) && ($parameter->getItemParameter() !== null)) {
            return $this->isFlaggedParameter($parameter->getItemParameter(), $isFlagged);
        }

        return false;
    }
}
