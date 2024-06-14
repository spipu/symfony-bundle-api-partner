<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter;

use DateTime;
use DateTimeInterface;
use Spipu\ApiPartnerBundle\Model\Parameter\DateTimeParameter;

class DateTimeParameterTest extends AbstractParameterTest
{
    public function testBase()
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

    public function testNotRequired()
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

    public function testRequired()
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
