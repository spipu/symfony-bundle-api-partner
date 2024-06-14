<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\ArrayParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class ArrayParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new ArrayParameter(),
            'array',
            'Array',
            [
                ['value' => 'string', 'message' => 'parameter must be an array'],
                ['value' => false, 'message' => 'parameter must be an array'],
                ['value' => date('Y-m-d H:i:s'), 'message' => 'parameter must be an array'],
                ['value' => 1, 'message' => 'parameter must be an array'],
                ['value' => -1, 'message' => 'parameter must be an array'],
                ['value' => 1.5, 'message' => 'parameter must be an array'],
                ['value' => -1.5, 'message' => 'parameter must be an array'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be an array'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be an array'],
            ],
            [
                ['value' => ['foo', 'bar'], 'result' => ['foo', 'bar']],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new ArrayParameter())->setRequired(false),
            'array',
            'Array',
            [],
            [['value' => null, 'result' => null]]
        );

        $this->fullTest(
            (new ArrayParameter())->setRequired(false)->setDefaultValue(['foo', 'bar']),
            'array',
            'Array',
            [],
            [['value' => null, 'result' => ['foo', 'bar']]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new ArrayParameter())->setRequired(true),
            'array',
            'Array',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }

    public function testMinItemInclusive()
    {
        $this->fullTest(
            (new ArrayParameter())->setMinItems(2, false),
            'array',
            'Array',
            [
                ['value' => array_fill(0, 0, 'foo'), 'message' => 'number of items must be equal or greater than 2'],
                ['value' => array_fill(0, 1, 'foo'), 'message' => 'number of items must be equal or greater than 2'],
            ],
            [
                ['value' => array_fill(0, 2, 'foo'), 'result' => array_fill(0, 2, 'foo')],
                ['value' => array_fill(0, 3, 'foo'), 'result' => array_fill(0, 3, 'foo')],
                ['value' => array_fill(0, 4, 'foo'), 'result' => array_fill(0, 4, 'foo')],
            ]
        );
    }

    public function testMinItemExclusive()
    {
        $this->fullTest(
            (new ArrayParameter())->setMinItems(2, true),
            'array',
            'Array',
            [
                ['value' => array_fill(0, 0, 'foo'), 'message' => 'number of items must be greater than 2'],
                ['value' => array_fill(0, 1, 'foo'), 'message' => 'number of items must be greater than 2'],
                ['value' => array_fill(0, 2, 'foo'), 'message' => 'number of items must be greater than 2'],
            ],
            [
                ['value' => array_fill(0, 3, 'foo'), 'result' => array_fill(0, 3, 'foo')],
                ['value' => array_fill(0, 4, 'foo'), 'result' => array_fill(0, 4, 'foo')],
            ]
        );
    }

    public function testMaxItemInclusive()
    {
        $this->fullTest(
            (new ArrayParameter())->setMaxItems(2, false),
            'array',
            'Array',
            [
                ['value' => array_fill(0, 4, 'foo'), 'message' => 'number of items must be equal or lower than 2'],
                ['value' => array_fill(0, 3, 'foo'), 'message' => 'number of items must be equal or lower than 2'],
            ],
            [
                ['value' => array_fill(0, 2, 'foo'), 'result' => array_fill(0, 2, 'foo')],
                ['value' => array_fill(0, 1, 'foo'), 'result' => array_fill(0, 1, 'foo')],
                ['value' => array_fill(0, 0, 'foo'), 'result' => array_fill(0, 0, 'foo')],
            ]
        );
    }

    public function testMaxItemExclusive()
    {
        $this->fullTest(
            (new ArrayParameter())->setMaxItems(2, true),
            'array',
            'Array',
            [
                ['value' => array_fill(0, 4, 'foo'), 'message' => 'number of items must be lower than 2'],
                ['value' => array_fill(0, 3, 'foo'), 'message' => 'number of items must be lower than 2'],
                ['value' => array_fill(0, 2, 'foo'), 'message' => 'number of items must be lower than 2'],
            ],
            [
                ['value' => array_fill(0, 1, 'foo'), 'result' => array_fill(0, 1, 'foo')],
                ['value' => array_fill(0, 0, 'foo'), 'result' => array_fill(0, 0, 'foo')],
            ]
        );
    }

    public function testValueType()
    {
        $this->fullTest(
            (new ArrayParameter())->setItemParameter(new IntegerParameter()),
            'array',
            'Array',
            [
                ['value' => ['a', 2], 'full_message' => 'test0[0] - parameter must be an integer'],
                ['value' => [1, 'b'], 'full_message' => 'test1[1] - parameter must be an integer'],
            ],
            [
                ['value' => [1, 2], 'result' => [1, 2]],
            ]
        );

        $this->fullTest(
            (new ArrayParameter())->setItemParameter(new StringParameter()),
            'array',
            'Array',
            [
                ['value' => ['a', 2], 'full_message' => 'test0[1] - parameter must be a string'],
                ['value' => [1, 'b'], 'full_message' => 'test1[0] - parameter must be a string'],
            ],
            [
                ['value' => ['a', 'b'], 'result' => ['a', 'b']],
            ]
        );
    }
}
