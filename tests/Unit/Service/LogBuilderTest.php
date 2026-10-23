<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Entity\ApiLogPartner;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Model\Request;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Model\ResponseFormat;
use Spipu\ApiPartnerBundle\Service\ApiResponseStatus;
use Spipu\ApiPartnerBundle\Service\LogBuilderFactory;
use Spipu\ApiPartnerBundle\Service\SecretParameterService;

class LogBuilderTest extends TestCase
{
    private const BODY = '{"name":"foo","password":"my-password"}';
    private const BODY_MASKED = '{"name":"foo","password":"###SECRET###"}';
    private const RESPONSE = ['name' => 'foo', 'token' => 'my-token'];
    private const RESPONSE_MASKED = '{"name":"foo","token":"###SECRET###"}';

    public function testWithoutRoute(): void
    {
        $log = $this->buildLog(null, (new Response())->setContentJson(self::RESPONSE));

        $this->assertSame(self::BODY, $log->getBodyString());
        $this->assertSame(json_encode(self::RESPONSE), $log->getResponseContent());
    }

    public function testWithRoute(): void
    {
        $log = $this->buildLog($this->getRoute(), (new Response())->setContentJson(self::RESPONSE));

        $this->assertSame(self::BODY_MASKED, $log->getBodyString());
        $this->assertSame(self::RESPONSE_MASKED, $log->getResponseContent());
    }

    public function testWithRouteAndTextResponse(): void
    {
        $log = $this->buildLog($this->getRoute(), (new Response())->setContentText('my-token'));

        $this->assertSame(self::BODY_MASKED, $log->getBodyString());
        $this->assertSame('my-token', $log->getResponseContent());
    }

    private function buildLog(?RouteInterface $route, Response $response): ApiLogPartner
    {
        $factory = new LogBuilderFactory(
            $this->createMock(EntityManagerInterface::class),
            new ApiResponseStatus(),
            new SecretParameterService()
        );

        $request = new Request();
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
        ]);

        $route = $this->createMock(RouteInterface::class);
        $route->method('getBodyParameters')->willReturn([
            'name'     => new StringParameter(),
            'password' => (new StringParameter())->setSecret(true),
        ]);
        $route->method('getResponseFormat')->willReturn($responseFormat);

        return $route;
    }
}
