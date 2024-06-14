<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model\Parameter;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\ParameterInterface;

abstract class AbstractParameterTest extends TestCase
{
    /**
     * @param object $parameter
     * @param string $code
     * @param string $name
     * @param array $badValues
     * @param array $goodValues
     * @throws RouteException
     */
    protected function fullTest(
        object $parameter,
        string $code,
        string $name,
        array $badValues,
        array $goodValues
    ) {
        $this->assertInstanceOf(ParameterInterface::class, $parameter);

        /** @var ParameterInterface $parameter */
        $this->assertSame($code, $parameter->getCode());
        $this->assertSame($name, $parameter->getName());

        foreach ($badValues as $key => $badValue) {
            $currentException = null;
            try {
                $parameter->validateValue('test' . $key, $badValue['value']);
            } catch (\Exception $e) {
                $currentException = $e;
            }
            if ($currentException === null) {
                echo "\n========[DEBUG-BEGIN]========\n";
                print_r($badValue);
                echo "\n========[DEBUG-END]========\n";
            }
            $expectedMessage = null;
            if (array_key_exists('message', $badValue)) {
                $expectedMessage = 'test' . $key . ' - ' . $badValue['message'];
            }
            if (array_key_exists('full_message', $badValue)) {
                $expectedMessage = $badValue['full_message'];
            }
            $this->assertNotNull($currentException);
            $this->assertInstanceOf(RouteException::class, $currentException);
            $this->assertSame($expectedMessage, $currentException->getMessage());
        }

        foreach ($goodValues as $goodValue) {
            $this->assertSame(
                $goodValue['result'],
                $parameter->validateValue('test', $goodValue['value'])
            );
        }
    }
}
