<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\Page;

use Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\AbstractParameterTest;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\Page\MaxParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class MaxParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $parameter = new MaxParameter();
        $this->assertInstanceOf(ParameterInterface::class, $parameter);
        $this->assertInstanceOf(IntegerParameter::class, $parameter);

        $this->fullTest(
            new MaxParameter(),
            'int',
            'Integer',
            [
                ['value' => 0, 'message' => 'value must be equal or greater than 1'],
                ['value' => 1001, 'message' => 'value must be equal or lower than 1000'],
            ],
            [
                ['value' => null, 'result' => 10],
                ['value' => 42, 'result' => 42],
            ]
        );
    }
}
