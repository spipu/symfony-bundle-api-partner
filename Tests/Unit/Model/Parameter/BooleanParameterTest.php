<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\BooleanParameter;

class BooleanParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new BooleanParameter(),
            'bool',
            'Boolean',
            [
                ['value' => 'string', 'message' => 'parameter must be a boolean'],
                ['value' => date('Y-m-d H:i:s'), 'message' => 'parameter must be a boolean'],
                ['value' => 2, 'message' => 'parameter must be a boolean'],
                ['value' => -2, 'message' => 'parameter must be a boolean'],
                ['value' => 1.5, 'message' => 'parameter must be a boolean'],
                ['value' => -1.5, 'message' => 'parameter must be a boolean'],
                ['value' => ['foo' => 'bar'], 'message' => 'parameter must be a boolean'],
                ['value' => [0 => 'foo', 2 => 'bar'], 'message' => 'parameter must be a boolean'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be a boolean'],
            ],
            [
                ['value' => false, 'result' => false],
                ['value' => 'false', 'result' => false],
                ['value' => '0', 'result' => false],
                ['value' => 0, 'result' => false],
                ['value' => true, 'result' => true],
                ['value' => 'true', 'result' => true],
                ['value' => '1', 'result' => true],
                ['value' => 1, 'result' => true],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new BooleanParameter())->setRequired(false),
            'bool',
            'Boolean',
            [],
            [['value' => null, 'result' => null]]
        );

        $this->fullTest(
            (new BooleanParameter())->setRequired(false)->setDefaultValue(true),
            'bool',
            'Boolean',
            [],
            [['value' => null, 'result' => true]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new BooleanParameter())->setRequired(true),
            'bool',
            'Boolean',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }
}
