<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model;

use App\Api\Route\Test\HelloWorld;
use App\Model\Partner;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Exception\RouteException;
use Spipu\ApiPartnerBundle\Model\Context;

class ContextTest extends TestCase
{
    public function testBase()
    {
        $partner = new Partner(true, '', '');
        $route = new HelloWorld();
        $context = new Context();

        $this->assertNull($context->getPartner());
        $context->setPartner($partner);
        $this->assertSame($partner, $context->getPartner());

        $context->setRoute($route);
        $this->assertSame($route, $context->getRoute());
    }

    public function testPath()
    {
        $params = ['string' => 'foo', 'int' => 1, 'bool' => true, 'float' => 1.5, 'array' => [1,3,5]];
        $context = new Context();
        $context->setPathParameters($params);

        foreach ($params as $key => $value) {
            $this->assertSame($value, $context->getPathParameter($key));
        }

        $this->expectException(RouteException::class);
        $context->getPathParameter('wrong');
    }

    public function testQuery()
    {
        $params = ['string' => 'foo', 'int' => 1, 'bool' => true, 'float' => 1.5, 'array' => [1,3,5]];
        $context = new Context();
        $context->setQueryParameters($params);

        foreach ($params as $key => $value) {
            $this->assertSame($value, $context->getQueryParameter($key));
        }

        $this->expectException(RouteException::class);
        $context->getQueryParameter('wrong');
    }

    public function testBody()
    {
        $params = ['string' => 'foo', 'int' => 1, 'bool' => true, 'float' => 1.5, 'array' => [1,3,5]];
        $context = new Context();
        $context->setBodyParameters($params);

        foreach ($params as $key => $value) {
            $this->assertSame($value, $context->getBodyParameter($key));
        }

        $this->expectException(RouteException::class);
        $context->getBodyParameter('wrong');
    }
}
