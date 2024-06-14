<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class StringParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new StringParameter(),
            'string',
            'String',
            [
                ['value' => false, 'message' => 'parameter must be a string'],
                ['value' => 1, 'message' => 'parameter must be a string'],
                ['value' => -1, 'message' => 'parameter must be a string'],
                ['value' => 1.5, 'message' => 'parameter must be a string'],
                ['value' => -1.5, 'message' => 'parameter must be a string'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be a string'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be a string'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be a string'],
            ],
            [
                ['value' => '', 'result' => ''],
                ['value' => 'foo', 'result' => 'foo'],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new StringParameter())->setRequired(false),
            'string',
            'String',
            [],
            [['value' => null, 'result' => null]]
        );

        $this->fullTest(
            (new StringParameter())->setRequired(false)->setDefaultValue('foo'),
            'string',
            'String',
            [],
            [['value' => null, 'result' => 'foo']]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new StringParameter())->setRequired(true),
            'string',
            'String',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }

    public function testMinInclusive()
    {
        $this->fullTest(
            (new StringParameter())->setMinLength(2, false),
            'string',
            'String',
            [
                ['value' => '', 'message' => 'string length must be equal or greater than 2'],
                ['value' => 'a', 'message' => 'string length must be equal or greater than 2'],
            ],
            [
                ['value' => 'aa', 'result' => 'aa'],
                ['value' => 'aaa', 'result' => 'aaa'],
                ['value' => 'aaaa', 'result' => 'aaaa'],
            ]
        );
    }

    public function testMinExclusive()
    {
        $this->fullTest(
            (new StringParameter())->setMinLength(2, true),
            'string',
            'String',
            [
                ['value' => '', 'message' => 'string length must be greater than 2'],
                ['value' => 'a', 'message' => 'string length must be greater than 2'],
                ['value' => 'aa', 'message' => 'string length must be greater than 2'],
            ],
            [
                ['value' => 'aaa', 'result' => 'aaa'],
                ['value' => 'aaaa', 'result' => 'aaaa'],
            ]
        );
    }

    public function testMaxInclusive()
    {
        $this->fullTest(
            (new StringParameter())->setMaxLength(2, false),
            'string',
            'String',
            [
                ['value' => 'aaaa', 'message' => 'string length must be equal or lower than 2'],
                ['value' => 'aaa', 'message' => 'string length must be equal or lower than 2'],
            ],
            [
                ['value' => 'aa', 'result' => 'aa'],
                ['value' => 'a', 'result' => 'a'],
                ['value' => '', 'result' => ''],
            ]
        );
    }

    public function testMaxExclusive()
    {
        $this->fullTest(
            (new StringParameter())->setMaxLength(2, true),
            'string',
            'String',
            [
                ['value' => 'aaaa', 'message' => 'string length must be lower than 2'],
                ['value' => 'aaa', 'message' => 'string length must be lower than 2'],
                ['value' => 'aa', 'message' => 'string length must be lower than 2'],
            ],
            [
                ['value' => 'a', 'result' => 'a'],
                ['value' => '', 'result' => ''],
            ]
        );
    }

    public function testEnum()
    {
        $this->fullTest(
            (new StringParameter())->setEnum(['yes', 'no']),
            'string',
            'String',
            [
                ['value' => '', 'message' => 'string value must be one of yes,no'],
                ['value' => 'foo', 'message' => 'string value must be one of yes,no'],
            ],
            [
                ['value' => 'yes', 'result' => 'yes'],
                ['value' => 'no', 'result' => 'no'],
            ]
        );
    }

    public function testPattern()
    {
        $pattern = '/^[0-9]{4}-[0-9]{2}$/';
        $this->fullTest(
            (new StringParameter())->setPattern($pattern),
            'string',
            'String',
            [
                ['value' => '', 'message' => 'string value must correspond to pattern '.$pattern],
                ['value' => 'foo', 'message' => 'string value must correspond to pattern '.$pattern],
            ],
            [
                ['value' => '2021-01', 'result' => '2021-01'],
            ]
        );
    }

    public function testOther()
    {
        $parameter = new StringParameter();
        $this->assertNull($parameter->getDescription());

        $parameter->setDescription('fake description');
        $this->assertSame('fake description', $parameter->getDescription());

        $this->assertFalse($parameter->isRequired());

        $parameter->setRequired(true);
        $this->assertTrue($parameter->isRequired());

        $parameter->setRequired(false);
        $this->assertFalse($parameter->isRequired());

        $this->assertSame(
            [null, null, null, null],
            [$parameter->getMinLength(), $parameter->getExclusiveMin(), $parameter->getMaxLength(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinLength(1, false);
        $this->assertSame(
            [1, false, null, null],
            [$parameter->getMinLength(), $parameter->getExclusiveMin(), $parameter->getMaxLength(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinLength(2, true);
        $this->assertSame(
            [2, true, null, null],
            [$parameter->getMinLength(), $parameter->getExclusiveMin(), $parameter->getMaxLength(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxLength(3, false);
        $this->assertSame(
            [2, true, 3, false],
            [$parameter->getMinLength(), $parameter->getExclusiveMin(), $parameter->getMaxLength(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxLength(4, true);
        $this->assertSame(
            [2, true, 4, true],
            [$parameter->getMinLength(), $parameter->getExclusiveMin(), $parameter->getMaxLength(), $parameter->getExclusiveMax()]
        );

        $this->assertNull($parameter->getPattern());
        $parameter->setPattern('test');
        $this->assertSame('test', $parameter->getPattern());

        $this->assertNull($parameter->getEnum());
        $parameter->setEnum(['foo', 'Bar']);
        $this->assertSame(['foo', 'Bar'], $parameter->getEnum());
    }
}
