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

use DateTime;
use DateTimeInterface;
use Exception;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\AbstractParameter;

class DateTimeParameter extends AbstractParameter
{
    private ?DateTimeInterface $defaultValue = null;

    public function getCode(): string
    {
        return 'datetime';
    }

    public function getName(): string
    {
        return 'DateTime';
    }

    public function setDefaultValue(?DateTimeInterface $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getDefaultValue(): ?DateTimeInterface
    {
        return $this->defaultValue;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return DateTimeInterface
     * @throws RouteException
     * @SuppressWarnings(PMD.StaticAccess)
     */
    protected function validateValueType(string $key, mixed $value): DateTimeInterface
    {
        if (!is_string($value)) {
            throw $this->createException($key, 'parameter must be a valid datetime');
        }

        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
            throw $this->createException($key, 'parameter must be a valid datetime');
        }

        $format = 'Y-m-d H:i:s';
        try {
            $dateTimeValue = DateTime::createFromFormat($format, $value);
            if ($dateTimeValue->format($format) !== $value) {
                throw new Exception('invalid');
            }
            return $dateTimeValue;
        } catch (Exception $e) {
            throw $this->createException($key, 'parameter must be a valid datetime');
        }
    }
}
