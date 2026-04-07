<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\Page;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\AbstractParameterTestCase;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\Page\MaxParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(MaxParameter::class)]
class MaxParameterTest extends AbstractParameterTestCase
{
    public function testBase(): void
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
