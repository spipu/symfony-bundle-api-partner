<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter\Page;

use App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter\AbstractParameterTest;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\Page\OffsetParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class OffsetParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $parameter = new OffsetParameter();
        $this->assertInstanceOf(ParameterInterface::class, $parameter);
        $this->assertInstanceOf(IntegerParameter::class, $parameter);

        $this->fullTest(
            new OffsetParameter(),
            'int',
            'Integer',
            [
                ['value' => -1, 'message' => 'value must be equal or greater than 0'],
            ],
            [
                ['value' => null, 'result' => 0],
                ['value' => 42, 'result' => 42],
            ]
        );
    }
}
