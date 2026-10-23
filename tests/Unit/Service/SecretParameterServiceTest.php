<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Parameter\ArrayParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Service\SecretParameterService;

class SecretParameterServiceTest extends TestCase
{
    public function testHasSecret(): void
    {
        $service = new SecretParameterService();

        $this->assertFalse($service->hasSecret([]));
        $this->assertFalse($service->hasSecret(['name' => new StringParameter()]));
        $this->assertTrue($service->hasSecret(['password' => (new StringParameter())->setSecret(true)]));
        $this->assertTrue($service->hasSecret($this->getParameters()));
    }

    public function testHasSecretInObject(): void
    {
        $service = new SecretParameterService();

        $parameter = (new ObjectParameter())
            ->addProperty('password', (new StringParameter())->setSecret(true));

        $this->assertTrue($service->hasSecret(['user' => $parameter]));
    }

    public function testHasSecretInArray(): void
    {
        $service = new SecretParameterService();

        $parameter = (new ArrayParameter())
            ->setItemParameter((new StringParameter())->setSecret(true));

        $this->assertTrue($service->hasSecret(['passwords' => $parameter]));
        $this->assertFalse($service->hasSecret(['items' => new ArrayParameter()]));
    }

    public function testMaskJsonWithoutSecret(): void
    {
        $service = new SecretParameterService();

        $json = '{"name":"foo",  "id":42}';
        $this->assertSame($json, $service->maskJson($json, ['name' => new StringParameter()]));
        $this->assertSame('not a json', $service->maskJson('not a json', ['name' => new StringParameter()]));
    }

    public function testMaskJson(): void
    {
        $service = new SecretParameterService();

        $json = json_encode([
            'name'     => 'foo',
            'password' => 'my-password',
            'user'     => ['login' => 'bar', 'token' => 'my-token'],
            'rows'     => [
                ['id' => 1, 'key' => 'key-1'],
                ['id' => 2, 'key' => 'key-2'],
                'bad-row',
            ],
            'codes'    => ['code-1', 'code-2'],
            'other'    => 'not-declared',
        ]);

        $expected = json_encode([
            'name'     => 'foo',
            'password' => SecretParameterService::SECRET_VALUE,
            'user'     => ['login' => 'bar', 'token' => SecretParameterService::SECRET_VALUE],
            'rows'     => [
                ['id' => 1, 'key' => SecretParameterService::SECRET_VALUE],
                ['id' => 2, 'key' => SecretParameterService::SECRET_VALUE],
                'bad-row',
            ],
            'codes'    => [SecretParameterService::SECRET_VALUE, SecretParameterService::SECRET_VALUE],
            'other'    => 'not-declared',
        ]);

        $this->assertSame($expected, $service->maskJson($json, $this->getParameters()));
    }

    public function testMaskJsonSecretWithBadType(): void
    {
        $service = new SecretParameterService();

        $parameters = ['user' => (new ObjectParameter())->setSecret(true)];

        $this->assertSame(
            json_encode(['user' => SecretParameterService::SECRET_VALUE]),
            $service->maskJson(json_encode(['user' => ['login' => 'foo']]), $parameters)
        );
    }

    public function testMaskJsonInvalidWithSecret(): void
    {
        $service = new SecretParameterService();

        $this->assertSame(
            SecretParameterService::SECRET_VALUE,
            $service->maskJson('{"password":"my-password"', $this->getParameters())
        );
        $this->assertSame(
            SecretParameterService::SECRET_VALUE,
            $service->maskJson('"my-password"', $this->getParameters())
        );
    }

    private function getParameters(): array
    {
        return [
            'name'     => new StringParameter(),
            'password' => (new StringParameter())->setSecret(true),
            'user'     => (new ObjectParameter())
                ->addProperty('login', new StringParameter())
                ->addProperty('token', (new StringParameter())->setSecret(true)),
            'rows'     => (new ArrayParameter())
                ->setItemParameter(
                    (new ObjectParameter())
                        ->addProperty('id', new IntegerParameter())
                        ->addProperty('key', (new StringParameter())->setSecret(true))
                ),
            'codes'    => (new ArrayParameter())
                ->setItemParameter((new StringParameter())->setSecret(true)),
        ];
    }
}
