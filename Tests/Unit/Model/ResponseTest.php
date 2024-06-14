<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Response;

class ResponseTest extends TestCase
{
    public function testStatus()
    {
        $context = new Response();

        $this->assertSame(200, $context->getCode());

        $context->setCode(404);
        $this->assertSame(404, $context->getCode());

        $context->setCode(500);
        $this->assertSame(500, $context->getCode());
    }

    public function testText()
    {
        $context = new Response();

        $context->setContentText('the result');
        $this->assertSame('application/text', $context->getContentType());
        $this->assertSame('the result', $context->getContent());
    }

    public function testCsv()
    {
        $context = new Response();

        $context->setContentCsv('the;result');
        $this->assertSame('application/csv', $context->getContentType());
        $this->assertSame('the;result', $context->getContent());
    }

    public function testJson()
    {
        $context = new Response();

        $expected = ['the','result'];
        $context->setContentJson($expected);
        $this->assertSame('application/json', $context->getContentType());
        $this->assertSame(json_encode($expected), $context->getContent());
    }
}
