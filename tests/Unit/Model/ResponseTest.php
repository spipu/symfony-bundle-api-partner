<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Response;

class ResponseTest extends TestCase
{
    public function testGeneric()
    {
        $response = new Response();

        $this->assertSame(200, $response->getCode());

        $response->setCode(404);
        $this->assertSame(404, $response->getCode());

        $response->setCode(500);
        $this->assertSame(500, $response->getCode());

        $this->assertNull($response->getLogError());
        $response->setLogError('fake error');
        $this->assertSame('fake error', $response->getLogError());

        $this->assertNull($response->getLogId());

        $response->setLogId(42);
        $this->assertSame(42, $response->getLogId());

    }

    public function testText()
    {
        $response = new Response();

        $response->setContentText('the result');
        $this->assertSame('application/text', $response->getContentType());
        $this->assertSame('the result', $response->getContent());
        $this->assertFalse($response->isBinaryContent());
    }

    public function testCsv()
    {
        $response = new Response();

        $response->setContentCsv('the;result');
        $this->assertSame('application/csv', $response->getContentType());
        $this->assertSame('the;result', $response->getContent());
        $this->assertFalse($response->isBinaryContent());
    }

    public function testJson()
    {
        $response = new Response();

        $expected = ['the','result'];
        $response->setContentJson($expected);
        $this->assertSame('application/json', $response->getContentType());
        $this->assertSame(json_encode($expected), $response->getContent());
        $this->assertFalse($response->isBinaryContent());
    }

    public function testPdf()
    {
        $response = new Response();

        $response->setContentPdf('filename.pdf', 'fake_content_pdf');
        $this->assertSame('application/pdf', $response->getContentType());
        $this->assertSame(['Content-Disposition' => 'attachment; filename=filename.pdf'], $response->getHeaders());
        $this->assertSame('fake_content_pdf', $response->getContent());
        $this->assertTrue($response->isBinaryContent());
    }

    public function testJpg()
    {
        $response = new Response();

        $response->setContentJpg('filename.pdf', 'fake_content_jpg');
        $this->assertSame('image/jpg', $response->getContentType());
        $this->assertSame(['Content-Disposition' => 'attachment; filename=filename.pdf'], $response->getHeaders());
        $this->assertSame('fake_content_jpg', $response->getContent());
        $this->assertTrue($response->isBinaryContent());
    }
}
