<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Service\SecretParameterService;
use Spipu\ApiPartnerBundle\Service\SensitiveParameterService;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(SecretParameterService::class)]
class SecretParameterServiceTest extends TestCase
{
    public function testBackwardCompatibility(): void
    {
        $service = new SecretParameterService();

        $parameters = [
            'name'     => new StringParameter(),
            'password' => (new StringParameter())->setSecret(true),
        ];

        $this->assertInstanceOf(SensitiveParameterService::class, $service);
        $this->assertSame('###SECRET###', SecretParameterService::SECRET_VALUE);
        $this->assertTrue($service->hasSecret($parameters));
        $this->assertSame(
            '{"name":"foo","password":"###SECRET###"}',
            $service->maskJson('{"name":"foo","password":"my-password"}', $parameters)
        );
    }
}
