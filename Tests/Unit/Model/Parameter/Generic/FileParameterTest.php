<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter\Generic;

use App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter\AbstractParameterTest;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Parameter\Generic\FileParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

class FileParameterTest extends AbstractParameterTest
{
    public function testOk()
    {
        $parameter = new FileParameter(['pdf', 'csv']);
        $this->assertInstanceOf(ParameterInterface::class, $parameter);
        $this->assertInstanceOf(ObjectParameter::class, $parameter);

        $this->fullTest(
            (new FileParameter(['pdf']))
                ->addProperty('filename', new StringParameter())
                ->addProperty('content', new StringParameter()),
            'object',
            'Object',
            [
                ['value' => 'string', 'message' => 'parameter must be an object'],
                ['value' => ['xxx', 'www'], 'message' => 'parameter must be an object'],
            ],
            [
                ['value' => ['filename' => 'test-name-file.pdf', 'content' => 'test_content'], 'result' => ['filename' => 'test-name-file.pdf',  'content' => 'test_content']],
            ]
        );
    }

    private function testTypeKo()
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionCode(3000);
        $this->expectExceptionMessage('Type of document undefined');

        new FileParameter(['foo']);
    }
}
