<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Model\ResponseFormat;
use Spipu\ApiPartnerBundle\Service\ApiResponseStatus;
use Spipu\ApiPartnerBundle\Service\LogBuilder;
use Spipu\ApiPartnerBundle\Service\LogBuilderFactory;
use Spipu\ApiPartnerBundle\Service\SensitiveParameterService;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(LogBuilder::class)]
class LogBuilderTest extends TestCase
{
    private const ROUTE = '/test/john@example.com';
    private const QUERY = 'name=Foo&email=john%40example.com';
    private const QUERY_MASKED = 'name=Foo&email=###PRIVACY###';
    private const BODY = '{"name":"foo","password":"my-password","customer":{"full_name":"John Doe"}}';
    private const BODY_MASKED = '{"name":"foo","password":"###SECRET###","customer":{"full_name":"###PRIVACY###"}}';
    private const RESPONSE = ['name' => 'foo', 'token' => 'my-token', 'email' => 'john@example.com'];
    private const RESPONSE_MASKED = '{"name":"foo","token":"###SECRET###","email":"###PRIVACY###"}';

    public function testWithoutRoute(): void
    {
        $log = $this->buildLog(null, (new Response())->setContentJson(self::RESPONSE));

        $this->assertSame(self::ROUTE, $log->getRoute());
        $this->assertSame(self::QUERY, $log->getQueryString());
        $this->assertSame(self::BODY, $log->getBodyString());
        $this->assertSame(json_encode(self::RESPONSE), $log->getResponseContent());
    }

    public function testWithRoute(): void
    {
        $log = $this->buildLog($this->getRoute(), (new Response())->setContentJson(self::RESPONSE));

        $this->assertSame('###PRIVACY###', $log->getRoute());
        $this->assertSame(self::QUERY_MASKED, $log->getQueryString());
        $this->assertSame(self::BODY_MASKED, $log->getBodyString());
        $this->assertSame(self::RESPONSE_MASKED, $log->getResponseContent());
    }

    public function testWithRouteAndTextResponse(): void
    {
        $log = $this->buildLog($this->getRoute(), (new Response())->setContentText('my-token'));

        $this->assertSame('###PRIVACY###', $log->getRoute());
        $this->assertSame(self::QUERY_MASKED, $log->getQueryString());
        $this->assertSame(self::BODY_MASKED, $log->getBodyString());
        $this->assertSame('my-token', $log->getResponseContent());
    }

    private function buildLog(?RouteInterface $route, Response $response): ApiLogPartner
    {
        $factory = new LogBuilderFactory(
            $this->createMock(EntityManagerInterface::class),
            new ApiResponseStatus(),
            new SensitiveParameterService()
        );

        $queryArray = [];
        parse_str(self::QUERY, $queryArray);

        $request = new Request();
        $request->setRoute(self::ROUTE);
        $request->setQueryString(self::QUERY);
        $request->setQueryArray($queryArray);
        $request->setBodyString(self::BODY);

        $builder = $factory->create();
        $builder->addRequest($request, $route);
        $builder->addResponse($response->setCode(200), $route);

        return $builder->create();
    }

    private function getRoute(): RouteInterface
    {
        $responseFormat = (new ResponseFormat('json'))->setJsonContent([
            'name'  => new StringParameter(),
            'token' => (new StringParameter())->setSecret(true),
            'email' => (new StringParameter())->setPrivacy(true),
        ]);

        $route = $this->createMock(RouteInterface::class);
        $route->method('getPathParameters')->willReturn([
            'email' => (new StringParameter())->setPrivacy(true),
        ]);
        $route->method('getQueryParameters')->willReturn([
            'name'  => new StringParameter(),
            'email' => (new StringParameter())->setPrivacy(true),
        ]);
        $route->method('getBodyParameters')->willReturn([
            'name'     => new StringParameter(),
            'password' => (new StringParameter())->setSecret(true),
            'customer' => (new ObjectParameter())
                ->addProperty('full_name', (new StringParameter())->setPrivacy(true)),
        ]);
        $route->method('getResponseFormat')->willReturn($responseFormat);

        return $route;
    }
}
