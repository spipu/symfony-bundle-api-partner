<?php

namespace App\Tests\Unit\Spareka\ApiPartnerBundle\Model\Parameter;

use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class ObjectParameterTest extends AbstractParameterTest
{
    public function testBase()
    {
        $this->fullTest(
            new ObjectParameter(),
            'object',
            'Object',
            [
                ['value' => 'string', 'message' => 'parameter must be an object'],
                ['value' => false, 'message' => 'parameter must be an object'],
                ['value' => date('Y-m-d H:i:s'), 'message' => 'parameter must be an object'],
                ['value' => 1, 'message' => 'parameter must be an object'],
                ['value' => 1.5, 'message' => 'parameter must be an object'],
                ['value' => ['foo', 'bar'], 'message' => 'parameter must be an object'],
            ],
            [
                ['value' => ['foo' => 'bar'], 'result' => ['foo' => 'bar']],
                ['value' => [0 => 'foo', 2 => 'bar'], 'result' => [0 => 'foo', 2 => 'bar']],
            ]
        );
    }

    public function testNotRequired()
    {
        $this->fullTest(
            (new ObjectParameter())->setRequired(false),
            'object',
            'Object',
            [],
            [['value' => null, 'result' => null]]
        );
    }

    public function testRequired()
    {
        $this->fullTest(
            (new ObjectParameter())->setRequired(true),
            'object',
            'Object',
            [['value' => null, 'message' => 'parameter is required']],
            []
        );
    }

    public function testProperties()
    {
        $this->fullTest(
            (new ObjectParameter())
                ->addProperty('foo', (new IntegerParameter())->setRequired(true))
                ->addProperty('bar', (new StringParameter())->setRequired(true)),
            'object',
            'Object',
            [
                ['value' => [], 'full_message' => 'test0.foo - parameter is required'],
                ['value' => ['foo' => 1], 'full_message' => 'test1.bar - parameter is required'],
                ['value' => ['bar' => 'a'], 'full_message' => 'test2.foo - parameter is required'],
                ['value' => ['foo' => 'a', 'bar' => 'b'], 'full_message' => 'test3.foo - parameter must be an integer'],
                ['value' => ['foo' => 1, 'bar' => 2], 'full_message' => 'test4.bar - parameter must be a string'],
            ],
            [
                ['value' => ['foo' => 1, 'bar' => 'b'], 'result' => ['foo' => 1, 'bar' => 'b']],
            ]
        );
    }
    public function testOther()
    {
        $parameter = new ObjectParameter();

        $this->assertSame([], $parameter->getProperties());

        $fooParameter = new StringParameter();
        $barParameter = new IntegerParameter();

        $parameter->addProperty('foo', $fooParameter);
        $parameter->addProperty('bar', $barParameter);
        $this->assertSame(['foo' => $fooParameter, 'bar' => $barParameter], $parameter->getProperties());
    }
}
