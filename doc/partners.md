# Partner Integration

[back](./README.md)

## Overview

The bundle does **not** provide a partner entity or a partner management UI. The application is responsible for storing partners and implementing the two required interfaces: `PartnerRepositoryInterface` and `RequestSecurityServiceInterface`.

## PartnerInterface

Your partner entity must implement `Spipu\ApiPartnerBundle\Entity\PartnerInterface`:

```php
interface PartnerInterface extends EntityInterface
{
    public function getId(): ?int;
    public function getApiName(): ?string;    // Display name of the partner
    public function getApiKey(): ?string;     // Public key sent with every request
    public function getApiSecretKey(): ?string; // Secret key used for request signing
    public function isApiEnabled(): ?bool;    // Whether this partner is active
}
```

## Example Partner Entity

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Spipu\ApiPartnerBundle\Entity\PartnerInterface;

#[ORM\Entity]
#[ORM\Table(name: 'api_partner')]
class ApiPartner implements PartnerInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $apiName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $apiKey = null;

    #[ORM\Column(length: 255)]
    private ?string $apiSecretKey = null;

    #[ORM\Column]
    private ?bool $apiEnabled = true;

    // Implement interface methods...
}
```

## PartnerRepositoryInterface

```php
interface PartnerRepositoryInterface
{
    public function getAllPartners(): array;                          // Used by the log grid (partner filter)
    public function getPartnerByApiKey(string $apiKey): ?PartnerInterface; // Used for authentication
    public function getPartnerById(int $id): ?PartnerInterface;      // Used by the log grid
}
```

## RequestSecurityServiceInterface

This interface lets the application control two things:

1. **Request validation** — `validate()` is called before any routing. Use it to verify request integrity (e.g., HMAC signature, timestamp replay protection).
2. **Route authorization** — `isRouteAllowed()` is called after the partner is identified. Use it to restrict which routes each partner can access.

```php
interface RequestSecurityServiceInterface
{
    public function validate(Request $request, SymfonyRequest $symfonyRequest): void;
    public function isRouteAllowed(RouteInterface $route, ?PartnerInterface $partner): bool;
}
```

If `validate()` should throw a `SecurityException` when the request is invalid.
If `isRouteAllowed()` returns `false`, the request is rejected with a 403.

## Authentication Flow

1. The client sends a request with their `apiKey` (e.g., as a header or query parameter).
2. `PartnerRepositoryInterface::getPartnerByApiKey()` looks up the partner.
3. `RequestSecurityServiceInterface::validate()` validates the request integrity.
4. `RequestSecurityServiceInterface::isRouteAllowed()` checks that the partner can call this route.
5. If all checks pass, the route's `ActionInterface::execute()` is called.

[back](./README.md)
