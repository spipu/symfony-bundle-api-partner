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

/**
 * @SuppressWarnings(PMD.NumberOfChildren)
 */
class StringParameter extends AbstractParameter
{
    private ?string $defaultValue = null;
    private ?int $minLength = null;
    private ?int $maxLength = null;
    private ?bool $exclusiveMin = null;
    private ?bool $exclusiveMax = null;
    private ?string $pattern = null;
    private ?array $enum = null;

    public function getCode(): string
    {
        return 'string';
    }

    public function getName(): string
    {
        return 'String';
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function getExclusiveMin(): ?bool
    {
        return $this->exclusiveMin;
    }

    public function getExclusiveMax(): ?bool
    {
        return $this->exclusiveMax;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function getEnum(): ?array
    {
        return $this->enum;
    }

    public function setDefaultValue(?string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param int|null $minLength
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMinLength(?int $minLength, bool $exclusive = false): self
    {
        $this->minLength = $minLength;
        $this->exclusiveMin = $exclusive;
        return $this;
    }

    /**
     * @param int|null $maxLength
     * @param bool $exclusive
     * @return $this
     * @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setMaxLength(?int $maxLength, bool $exclusive = false): self
    {
        $this->maxLength = $maxLength;
        $this->exclusiveMax = $exclusive;
        return $this;
    }

    public function setPattern(?string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function setEnum(?array $enum): self
    {
        $this->enum = $enum;
        return $this;
    }

    protected function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    protected function validateValueType(string $key, mixed $value): string
    {
        if (!is_string($value)) {
            throw $this->createException($key, 'parameter must be a string');
        }

        $this->validateMinLength($key, $value);
        $this->validateMaxLength($key, $value);
        $this->validatePattern($key, $value);
        $this->validateEnum($key, $value);

        return $value;
    }

    private function validateMinLength(string $key, string $value): void
    {
        if ($this->minLength === null) {
            return;
        }

        $length = mb_strlen($value);

        if ($this->exclusiveMin && $this->minLength >= $length) {
            throw $this->createException($key, 'string length must be greater than ' . $this->minLength);
        }

        if (!$this->exclusiveMin && $this->minLength > $length) {
            throw $this->createException($key, 'string length must be equal or greater than ' . $this->minLength);
        }
    }

    private function validateMaxLength(string $key, string $value): void
    {
        if ($this->maxLength === null) {
            return;
        }
        $length = mb_strlen($value);

        if ($this->exclusiveMax && $this->maxLength <= $length) {
            throw $this->createException($key, 'string length must be lower than ' . $this->maxLength);
        }

        if (!$this->exclusiveMax && $this->maxLength < $length) {
            throw $this->createException($key, 'string length must be equal or lower than ' . $this->maxLength);
        }
    }

    private function validatePattern(string $key, string $value): void
    {
        if ($this->pattern === null) {
            return;
        }

        if (!preg_match($this->pattern, $value)) {
            throw $this->createException($key, 'string value must correspond to pattern ' . $this->pattern);
        }
    }

    private function validateEnum(string $key, string $value): void
    {
        if ($this->enum === null) {
            return;
        }

        if (!in_array($value, $this->enum)) {
            throw $this->createException($key, 'string value must be one of ' . implode(',', $this->enum));
        }
    }
}
