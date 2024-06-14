<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\NumberParameter;

class NumberParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new NumberParameter(),
            'number',
            'Number',
            [
                ['value' => 'string', 'message' => 'parameter must be a number'],
                ['value' => date('Y-m-d H:i:s'), 'message' => 'parameter must be a number'],
                ['value' => false, 'message' => 'parameter must be a number'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be a number'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be a number'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be a number'],
            ],
            [
                ['value' => 0, 'result' => 0.],
                ['value' => 5, 'result' => 5.],
                ['value' => -5, 'result' => -5.],
                ['value' => 4.2, 'result' => 4.2],
                ['value' => -4.2, 'result' => -4.2],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new NumberParameter())->setRequired(false),
            'number',
            'Number',
            [],
            [['value' => null, 'result' => null]]
        );

        $this->fullTest(
            (new NumberParameter())->setRequired(false)->setDefaultValue(4.2),
            'number',
            'Number',
            [],
            [['value' => null, 'result' => 4.2]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new NumberParameter())->setRequired(true),
            'number',
            'Number',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }

    public function testMinInclusive()
    {
        $this->fullTest(
            (new NumberParameter())->setMinValue(2.5, false),
            'number',
            'Number',
            [
                ['value' => 0.5, 'message' => 'value must be equal or greater than 2.5'],
                ['value' => 1.5, 'message' => 'value must be equal or greater than 2.5'],
            ],
            [
                ['value' => 2.5, 'result' => 2.5],
                ['value' => 3.5, 'result' => 3.5],
                ['value' => 4.5, 'result' => 4.5],
            ]
        );
    }

    public function testMinExclusive()
    {
        $this->fullTest(
            (new NumberParameter())->setMinValue(2.5, true),
            'number',
            'Number',
            [
                ['value' => 0.5, 'message' => 'value must be greater than 2.5'],
                ['value' => 1.5, 'message' => 'value must be greater than 2.5'],
                ['value' => 2.5, 'message' => 'value must be greater than 2.5'],
            ],
            [
                ['value' => 3.5, 'result' => 3.5],
                ['value' => 4.5, 'result' => 4.5],
            ]
        );
    }

    public function testMaxInclusive()
    {
        $this->fullTest(
            (new NumberParameter())->setMaxValue(2.5, false),
            'number',
            'Number',
            [
                ['value' => 4.5, 'message' => 'value must be equal or lower than 2.5'],
                ['value' => 3.5, 'message' => 'value must be equal or lower than 2.5'],
            ],
            [
                ['value' => 2.5, 'result' => 2.5],
                ['value' => 1.5, 'result' => 1.5],
                ['value' => 0.5, 'result' => 0.5],
            ]
        );
    }

    public function testMaxExclusive()
    {
        $this->fullTest(
            (new NumberParameter())->setMaxValue(2.5, true),
            'number',
            'Number',
            [
                ['value' => 4.5, 'message' => 'value must be lower than 2.5'],
                ['value' => 3.5, 'message' => 'value must be lower than 2.5'],
                ['value' => 2.5, 'message' => 'value must be lower than 2.5'],
            ],
            [
                ['value' => 1.5, 'result' => 1.5],
                ['value' => 0.5, 'result' => 0.5],
            ]
        );
    }

    public function testOther()
    {
        $parameter = new NumberParameter();

        $this->assertSame(
            [null, null, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinValue(1.1, false);
        $this->assertSame(
            [1.1, false, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinValue(2.2, true);
        $this->assertSame(
            [2.2, true, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxValue(3.3, false);
        $this->assertSame(
            [2.2, true, 3.3, false],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxValue(4.4, true);
        $this->assertSame(
            [2.2, true, 4.4, true],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );
    }
}
