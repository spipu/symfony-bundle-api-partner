<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter;

use DateTime;
use DateTimeInterface;
use Spipu\ApiPartnerBundle\Model\Parameter\DateParameter;

class DateParameterTest extends AbstractParameterTest
{
    public function testBase()
    {

        $this->fullTest(
            new DateParameter(),
            'date',
            'Date',
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
                ['value' => '0000-00-00', 'message' => 'parameter must be a valid datetime'],
                ['value' => '9999-99-99', 'message' => 'parameter must be a valid datetime'],
            ],
            []
        );

        $parameter = new DateParameter();

        $value = new DateTime();
        $result = $parameter->validateValue('test', $value->format('Y-m-d'));
        $this->assertInstanceOf(DateTimeInterface::class, $result);
        $this->assertSame($value->format('Y-m-d 00:00:00'), $result->format('Y-m-d H:i:s'));
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new DateParameter())->setRequired(false),
            'date',
            'Date',
            [],
            [['value' => null, 'result' => null]]
        );

        $result = new DateTime();

        $this->fullTest(
            (new DateParameter())->setRequired(false)->setDefaultValue($result),
            'date',
            'Date',
            [],
            [['value' => null, 'result' => $result]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new DateParameter())->setRequired(true),
            'date',
            'Date',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }
}
