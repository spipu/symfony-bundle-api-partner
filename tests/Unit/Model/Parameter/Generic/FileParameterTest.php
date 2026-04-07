<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\Generic;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter\AbstractParameterTestCase;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Parameter\Generic\FileParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(FileParameter::class)]
class FileParameterTest extends AbstractParameterTestCase
{
    public function testOk(): void
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

    public function testTypeKo(): void
    {
        $this->expectException(RouteException::class);
        $this->expectExceptionCode(3000);
        $this->expectExceptionMessage('Type of document undefined');

        new FileParameter([]);
    }
}
