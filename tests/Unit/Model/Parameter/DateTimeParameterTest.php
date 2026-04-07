<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter;

use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Spipu\ApiPartnerBundle\Model\Parameter\DateTimeParameter;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(DateTimeParameter::class)]
class DateTimeParameterTest extends AbstractParameterTestCase
{
    public function testBase(): void
    {

        $this->fullTest(
            new DateTimeParameter(),
            'datetime',
            'DateTime',
            [
                ['value' => 'string', 'message' => 'parameter must be a valid datetime'],
                ['value' => false, 'message' => 'parameter must be a valid datetime'],
                ['value' => 1, 'message' => 'parameter must be a valid datetime'],
                ['value' => -1, 'message' => 'parameter must be a valid datetime'],
                ['value' => 1.5, 'message' => 'parameter must be a valid datetime'],
                ['value' => -1.5, 'message' => 'parameter must be a valid datetime'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be a valid datetime'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be a valid datetime'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be a valid datetime'],
                ['value' => '0000-00-00 00:00:00', 'message' => 'parameter must be a valid datetime'],
                ['value' => '9999-99-99 99:99:99', 'message' => 'parameter must be a valid datetime'],
            ],
            []
        );

        $parameter = new DateTimeParameter();

        $value = new DateTime();
        $result = $parameter->validateValue('test', $value->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame($value->format('Y-m-d H:i:s'), $result->format('Y-m-d H:i:s'));
    }

    public function testNotRequired(): void
    {
        $this->fullTest(
            (new DateTimeParameter())->setRequired(false),
            'datetime',
            'DateTime',
            [],
            [['value' => null, 'result' => null]]
        );

        $result = new DateTime();

        $this->fullTest(
            (new DateTimeParameter())->setRequired(false)->setDefaultValue($result),
            'datetime',
            'DateTime',
            [],
            [['value' => null, 'result' => $result]]
        );
    }

    public function testRequired(): void
    {
        $this->fullTest(
            (new DateTimeParameter())->setRequired(true),
            'datetime',
            'DateTime',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }
}
