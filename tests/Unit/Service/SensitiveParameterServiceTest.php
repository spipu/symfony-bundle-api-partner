<?php

declare(strict_types=1);

namespace Spipu\ApiPartnerBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Spipu\ApiPartnerBundle\Model\Parameter\ArrayParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\BooleanParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\ObjectParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;
use Spipu\ApiPartnerBundle\Service\SensitiveParameterService;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(SensitiveParameterService::class)]
class SensitiveParameterServiceTest extends TestCase
{
    private const SECRET = SensitiveParameterService::SECRET_VALUE;
    private const PRIVACY = SensitiveParameterService::PRIVACY_VALUE;

    public function testHasSecret(): void
    {
        $service = new SensitiveParameterService();

        $this->assertFalse($service->hasSecret([]));
        $this->assertFalse($service->hasSecret(['name' => new StringParameter()]));
        $this->assertFalse($service->hasSecret(['email' => (new StringParameter())->setPrivacy(true)]));
        $this->assertTrue($service->hasSecret(['password' => (new StringParameter())->setSecret(true)]));
        $this->assertTrue($service->hasSecret($this->getParameters()));
    }

    public function testHasSecretInObject(): void
    {
        $service = new SensitiveParameterService();

        $parameter = (new ObjectParameter())
            ->addProperty('password', (new StringParameter())->setSecret(true))
        ;

        $this->assertTrue($service->hasSecret(['user' => $parameter]));
    }

    public function testHasSecretInArray(): void
    {
        $service = new SensitiveParameterService();

        $parameter = (new ArrayParameter())
            ->setItemParameter((new StringParameter())->setSecret(true))
        ;

        $this->assertTrue($service->hasSecret(['passwords' => $parameter]));
        $this->assertFalse($service->hasSecret(['items' => new ArrayParameter()]));
    }

    public function testHasPrivacy(): void
    {
        $service = new SensitiveParameterService();

        $this->assertFalse($service->hasPrivacy([]));
        $this->assertFalse($service->hasPrivacy(['name' => new StringParameter()]));
        $this->assertFalse($service->hasPrivacy(['password' => (new StringParameter())->setSecret(true)]));
        $this->assertTrue($service->hasPrivacy(['email' => (new StringParameter())->setPrivacy(true)]));
        $this->assertTrue($service->hasPrivacy($this->getPrivacyParameters()));
    }

    public function testHasPrivacyInObject(): void
    {
        $service = new SensitiveParameterService();

        $parameter = (new ObjectParameter())
            ->addProperty('email', (new StringParameter())->setPrivacy(true))
        ;

        $this->assertTrue($service->hasPrivacy(['user' => $parameter]));
        $this->assertFalse($service->hasSecret(['user' => $parameter]));
    }

    public function testHasPrivacyInArray(): void
    {
        $service = new SensitiveParameterService();

        $parameter = (new ArrayParameter())
            ->setItemParameter((new StringParameter())->setPrivacy(true))
        ;

        $this->assertTrue($service->hasPrivacy(['emails' => $parameter]));
        $this->assertFalse($service->hasPrivacy(['items' => new ArrayParameter()]));
    }

    public function testMaskJsonWithoutSensitive(): void
    {
        $service = new SensitiveParameterService();

        $json = '{"name":"foo",  "id":42}';
        $this->assertSame($json, $service->maskJson($json, ['name' => new StringParameter()]));
        $this->assertSame('not a json', $service->maskJson('not a json', ['name' => new StringParameter()]));
    }

    public function testMaskJson(): void
    {
        $service = new SensitiveParameterService();

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
            'password' => self::SECRET,
            'user'     => ['login' => 'bar', 'token' => self::SECRET],
            'rows'     => [
                ['id' => 1, 'key' => self::SECRET],
                ['id' => 2, 'key' => self::SECRET],
                self::SECRET,
            ],
            'codes'    => [self::SECRET, self::SECRET],
            'other'    => 'not-declared',
        ]);

        $this->assertSame($expected, $service->maskJson($json, $this->getParameters()));
    }

    public function testMaskJsonSecretWithBadType(): void
    {
        $service = new SensitiveParameterService();

        $parameters = ['user' => (new ObjectParameter())->setSecret(true)];

        $this->assertSame(
            json_encode(['user' => self::SECRET]),
            $service->maskJson(json_encode(['user' => ['login' => 'foo']]), $parameters)
        );
    }

    public function testMaskJsonInvalidWithSecret(): void
    {
        $service = new SensitiveParameterService();

        $this->assertSame(self::SECRET, $service->maskJson('{"password":"my-password"', $this->getParameters()));
        $this->assertSame(self::SECRET, $service->maskJson('"my-password"', $this->getParameters()));
    }

    public function testMaskJsonPrivacy(): void
    {
        $service = new SensitiveParameterService();

        $json = json_encode([
            'id'        => 42,
            'email'     => 'john@example.com',
            'customer'  => ['full_name' => 'John Doe', 'newsletter' => true],
            'addresses' => [
                ['city' => 'Paris', 'street' => '1 rue de Paris'],
                ['city' => 'Lyon', 'street' => '2 rue de Lyon'],
            ],
            'phones'    => ['0102030405', '0607080910'],
        ]);

        $expected = json_encode([
            'id'        => 42,
            'email'     => self::PRIVACY,
            'customer'  => ['full_name' => self::PRIVACY, 'newsletter' => true],
            'addresses' => [
                ['city' => 'Paris', 'street' => self::PRIVACY],
                ['city' => 'Lyon', 'street' => self::PRIVACY],
            ],
            'phones'    => [self::PRIVACY, self::PRIVACY],
        ]);

        $this->assertSame($expected, $service->maskJson($json, $this->getPrivacyParameters()));
    }

    public function testMaskJsonSecretAndPrivacyOnSameParameter(): void
    {
        $service = new SensitiveParameterService();

        $parameters = ['token' => (new StringParameter())->setSecret(true)->setPrivacy(true)];

        $this->assertSame(
            json_encode(['token' => self::SECRET]),
            $service->maskJson(json_encode(['token' => 'my-token']), $parameters)
        );
    }

    public function testMaskJsonPrivacyWithBadShape(): void
    {
        $service = new SensitiveParameterService();

        $parameters = $this->getPrivacyParameters();

        $this->assertSame(
            json_encode(['phones' => self::PRIVACY]),
            $service->maskJson(json_encode(['phones' => '0102030405']), $parameters)
        );
        $this->assertSame(
            json_encode(['customer' => self::PRIVACY]),
            $service->maskJson(json_encode(['customer' => 'John Doe']), $parameters)
        );
        $this->assertSame(
            json_encode(['customer' => self::PRIVACY]),
            $service->maskJson(json_encode(['customer' => ['John Doe']]), $parameters)
        );
        $this->assertSame(
            json_encode(['addresses' => self::PRIVACY]),
            $service->maskJson(json_encode(['addresses' => ['home' => ['street' => '1 rue de Paris']]]), $parameters)
        );
        $this->assertSame(
            json_encode(['customer' => []]),
            $service->maskJson(json_encode(['customer' => []]), $parameters)
        );
        $this->assertSame(
            json_encode(['user' => self::SECRET]),
            $service->maskJson(json_encode(['user' => 'my-token']), $this->getParameters())
        );
    }

    public function testMaskJsonInvalidWithPrivacy(): void
    {
        $service = new SensitiveParameterService();

        $parameters = $this->getPrivacyParameters();
        $this->assertSame(self::PRIVACY, $service->maskJson('{"email":"john@example.com"', $parameters));
        $this->assertSame(self::PRIVACY, $service->maskJson('"john@example.com"', $parameters));

        $parameters = $parameters + $this->getParameters();
        $this->assertSame(self::SECRET, $service->maskJson('{"email":"john@example.com"', $parameters));
    }

    public function testMaskQueryStringWithoutSensitive(): void
    {
        $service = new SensitiveParameterService();

        $this->assertSame(
            'name=Foo&name=Bar',
            $service->maskQueryString('name=Foo&name=Bar', ['name' => 'Bar'], ['name' => new StringParameter()])
        );
    }

    public function testMaskQueryStringPrivacy(): void
    {
        $service = new SensitiveParameterService();

        $parameters = [
            'name'  => new StringParameter(),
            'email' => (new StringParameter())->setPrivacy(true),
        ];

        $query = 'name=Foo&email=a%40b.c';
        $this->assertSame(
            'name=Foo&email=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $parameters)
        );

        $query = 'name=Foo+Bar/é&email=x';
        $this->assertSame(
            'name=Foo%20Bar%2F%C3%A9&email=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $parameters)
        );

        $query = 'name=Foo&email[]=x';
        $this->assertSame(
            'name=Foo&email=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $parameters)
        );
    }

    public function testMaskQueryStringPrivacyWholeValue(): void
    {
        $service = new SensitiveParameterService();

        $parameters = [
            'emails' => (new ArrayParameter())->setPrivacy(true),
            'user'   => (new ObjectParameter())->setPrivacy(true),
        ];

        $query = 'emails[]=a%40b.c&emails[]=c%40d.e&user[email]=a%40b.c';
        $this->assertSame(
            'emails=' . self::PRIVACY . '&user=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $parameters)
        );
    }

    public function testMaskQueryStringPrivacyAtDepth(): void
    {
        $service = new SensitiveParameterService();

        $query = 'customer[full_name]=John&customer[newsletter]=1&addresses[0][city]=Paris&addresses[0][street]=rue';
        $this->assertSame(
            'customer%5Bfull_name%5D=' . self::PRIVACY . '&customer%5Bnewsletter%5D=1'
            . '&addresses%5B0%5D%5Bcity%5D=Paris&addresses%5B0%5D%5Bstreet%5D=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $this->getPrivacyParameters())
        );
    }

    public function testMaskQueryStringPrivacyWithBadShape(): void
    {
        $service = new SensitiveParameterService();

        $query = 'phones=0102030405&customer=John';
        $this->assertSame(
            'phones=' . self::PRIVACY . '&customer=' . self::PRIVACY,
            $service->maskQueryString($query, $this->parseQuery($query), $this->getPrivacyParameters())
        );

        $parameters = ['emails' => (new ArrayParameter())->setItemParameter((new StringParameter())->setPrivacy(true))];
        $this->assertSame(
            'emails=' . self::PRIVACY,
            $service->maskQueryString(
                'emails=john%40example.com',
                $this->parseQuery('emails=john%40example.com'),
                $parameters
            )
        );
    }

    public function testMaskRoute(): void
    {
        $service = new SensitiveParameterService();

        $this->assertSame('/customer/42', $service->maskRoute('/customer/42', []));
        $this->assertSame('/customer/42', $service->maskRoute('/customer/42', ['id' => new IntegerParameter()]));
        $this->assertSame(
            self::PRIVACY,
            $service->maskRoute('/customer/john@example.com', ['email' => (new StringParameter())->setPrivacy(true)])
        );
    }

    public function testMaskRoutePrivacyAtDepth(): void
    {
        $service = new SensitiveParameterService();

        $parameters = [
            'filter' => (new ObjectParameter())
                ->addProperty('email', (new StringParameter())->setPrivacy(true)),
        ];

        $this->assertSame(self::PRIVACY, $service->maskRoute('/customer/john@example.com', $parameters));
    }

    private function parseQuery(string $query): array
    {
        $values = [];
        parse_str($query, $values);

        return $values;
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

    private function getPrivacyParameters(): array
    {
        return [
            'id'        => new IntegerParameter(),
            'email'     => (new StringParameter())->setPrivacy(true),
            'customer'  => (new ObjectParameter())
                ->addProperty('full_name', (new StringParameter())->setPrivacy(true))
                ->addProperty('newsletter', new BooleanParameter()),
            'addresses' => (new ArrayParameter())
                ->setItemParameter(
                    (new ObjectParameter())
                        ->addProperty('city', new StringParameter())
                        ->addProperty('street', (new StringParameter())->setPrivacy(true))
                ),
            'phones'    => (new ArrayParameter())
                ->setItemParameter((new StringParameter())->setPrivacy(true)),
        ];
    }
}
