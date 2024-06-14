<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;

class IntegerParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new IntegerParameter(),
            'int',
            'Integer',
            [
                ['value' => 'string', 'message' => 'parameter must be an integer'],
                ['value' => date('Y-m-d H:i:s'), 'message' => 'parameter must be an integer'],
                ['value' => false, 'message' => 'parameter must be an integer'],
                ['value' => 1.5, 'message' => 'parameter must be an integer'],
                ['value' => -1.5, 'message' => 'parameter must be an integer'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be an integer'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be an integer'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be an integer'],
            ],
            [
                ['value' => 0, 'result' => 0],
                ['value' => 5, 'result' => 5],
                ['value' => -5, 'result' => -5],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new IntegerParameter())->setRequired(false),
            'int',
            'Integer',
            [],
            [['value' => null, 'result' => null]]
        );

        $this->fullTest(
            (new IntegerParameter())->setRequired(false)->setDefaultValue(42),
            'int',
            'Integer',
            [],
            [['value' => null, 'result' => 42]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new IntegerParameter())->setRequired(true),
            'int',
            'Integer',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }

    public function testMinInclusive()
    {
        $this->fullTest(
            (new IntegerParameter())->setMinValue(2, false),
            'int',
            'Integer',
            [
                ['value' => 0, 'message' => 'value must be equal or greater than 2'],
                ['value' => 1, 'message' => 'value must be equal or greater than 2'],
            ],
            [
                ['value' => 2, 'result' => 2],
                ['value' => 3, 'result' => 3],
                ['value' => 4, 'result' => 4],
            ]
        );
    }

    public function testMinExclusive()
    {
        $this->fullTest(
            (new IntegerParameter())->setMinValue(2, true),
            'int',
            'Integer',
            [
                ['value' => 0, 'message' => 'value must be greater than 2'],
                ['value' => 1, 'message' => 'value must be greater than 2'],
                ['value' => 2, 'message' => 'value must be greater than 2'],
            ],
            [
                ['value' => 3, 'result' => 3],
                ['value' => 4, 'result' => 4],
            ]
        );
    }

    public function testMaxInclusive()
    {
        $this->fullTest(
            (new IntegerParameter())->setMaxValue(2, false),
            'int',
            'Integer',
            [
                ['value' => 4, 'message' => 'value must be equal or lower than 2'],
                ['value' => 3, 'message' => 'value must be equal or lower than 2'],
            ],
            [
                ['value' => 2, 'result' => 2],
                ['value' => 1, 'result' => 1],
                ['value' => 0, 'result' => 0],
            ]
        );
    }

    public function testMaxExclusive()
    {
        $this->fullTest(
            (new IntegerParameter())->setMaxValue(2, true),
            'int',
            'Integer',
            [
                ['value' => 4, 'message' => 'value must be lower than 2'],
                ['value' => 3, 'message' => 'value must be lower than 2'],
                ['value' => 2, 'message' => 'value must be lower than 2'],
            ],
            [
                ['value' => 1, 'result' => 1],
                ['value' => 0, 'result' => 0],
            ]
        );
    }

    public function testOther()
    {
        $parameter = new IntegerParameter();

        $this->assertSame(
            [null, null, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinValue(5, false);
        $this->assertSame(
            [5, false, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMinValue(6, true);
        $this->assertSame(
            [6, true, null, null],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxValue(7, false);
        $this->assertSame(
            [6, true, 7, false],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );

        $parameter->setMaxValue(8, true);
        $this->assertSame(
            [6, true, 8, true],
            [$parameter->getMinValue(), $parameter->getExclusiveMin(), $parameter->getMaxValue(), $parameter->getExclusiveMax()]
        );
    }
}
