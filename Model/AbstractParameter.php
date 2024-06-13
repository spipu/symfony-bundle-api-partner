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

use Spipu\ApiPartnerBundle\Exception\RouteException;

abstract class AbstractParameter implements ParameterInterface
{
    private ?string $description = null;
    protected bool $required = false;

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function validateValue(string $key, $value)
    {
        if ($value === null) {
            if ($this->required) {
                throw $this->createException($key, 'parameter is required');
            }

            return $this->getDefaultValue();
        }

        return $this->validateValueType($key, $value);
    }

    protected function createException(string $key, string $message): RouteException
    {
        return new RouteException($key . ' - ' . $message);
    }

    /**
     * @return mixed
     */
    abstract protected function getDefaultValue();

    /**
     * @return mixed
     */
    abstract protected function validateValueType(string $key, $value);
}
