<?php

namespace Spipu\ApiPartnerBundle\Tests\Unit\Model;

use App\Model\Partner;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Request;

class RequestTest extends TestCase
{
    public function testBase()
    {
        $partner = new Partner(true, 'key', 'secret');

        $request = new Request();
        $request->setApiKey('fake_api_key');
        $request->setRequestTime(123456789);
        $request->setRequestHash('fake_request_hash');
        $request->setRoute('/helloworld');
        $request->setMethod('GET');
        $request->setQueryString('fake_query_string');
        $request->setQueryArray(['fake', 'query', 'array']);
        $request->setBodyString('fake_body_string');
        $request->setPartner($partner);
        $request->setUserAgent('fake_user_agent');
        $request->setUserIp('fake_user_ip');

        $this->assertSame('fake_api_key', $request->getApiKey());
        $this->assertSame(123456789, $request->getRequestTime());
        $this->assertSame('fake_request_hash', $request->getRequestHash());
        $this->assertSame('/helloworld', $request->getRoute());
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('fake_query_string', $request->getQueryString());
        $this->assertSame(['fake', 'query', 'array'], $request->getQueryArray());
        $this->assertSame('fake_body_string', $request->getBodyString());
        $this->assertSame([], $request->getBodyArray());
        $this->assertSame($partner, $request->getPartner());
        $this->assertSame('fake_user_agent', $request->getUserAgent());
        $this->assertSame('fake_user_ip', $request->getUserIp());

        $expected = ['fake', 'body', 'array'];
        $request->setBodyString(json_encode($expected));
        $this->assertSame(json_encode($expected), $request->getBodyString());
        $this->assertSame($expected, $request->getBodyArray());
    }
}
