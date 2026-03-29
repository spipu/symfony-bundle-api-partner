# Installing Spipu API Partner Bundle

[back](./README.md)

## Requirements

- PHP 8.1+
- Symfony 6.4+
- `spipu/core-bundle`
- `spipu/ui-bundle`
- `spipu/configuration-bundle`

## Installation

```bash
composer require spipu/api-partner-bundle
```

## Configuration

### 1. Register the bundle

In `config/bundles.php`:

```php
return [
    // ...
    Spipu\CoreBundle\SpipuCoreBundle::class => ['all' => true],
    Spipu\UiBundle\SpipuUiBundle::class => ['all' => true],
    Spipu\ConfigurationBundle\SpipuConfigurationBundle::class => ['all' => true],
    Spipu\ApiPartnerBundle\SpipuApiPartnerBundle::class => ['all' => true],
];
```

### 2. Create application controllers

The bundle does not provide its own routes. The application must create:

**API entry point** — routes all API calls to `ApiControllerService`:

```php
namespace App\Controller\ApiPartner;

use Spipu\ApiPartnerBundle\Service\ApiControllerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api')]
class ApiEntryPointController extends AbstractController
{
    #[Route(path: '{route<.*>}', name: 'api_entrypoint')]
    public function entryPointAction(
        ApiControllerService $apiControllerService,
        SymfonyRequest $symfonyRequest,
        string $route
    ): Response {
        return $apiControllerService->entryPointAction($symfonyRequest, $route);
    }
}
```

**Admin log viewer** — renders the API log grid using `ApiPartnerLogGrid`:

```php
#[Route(path: '/api-partner/log')]
class ApiLogController extends AbstractController
{
    #[Route(path: '/', name: 'app_api_partner_log_list')]
    public function index(GridFactory $gridFactory, ApiPartnerLogGrid $grid): Response
    {
        $manager = $gridFactory->create($grid);
        if ($manager->validate()) {
            return $manager->getResponse();
        }
        return $this->render('api_partner/log/index.html.twig', ['manager' => $manager]);
    }
}
```

### 3. Run database migrations

The bundle provides the `ApiLogPartner` entity to store request logs:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### 4. Implement PartnerRepositoryInterface

Create a service that looks up partners by API key. The bundle does **not** provide a partner entity or CRUD UI — this is the responsibility of the application.

```php
use Spipu\ApiPartnerBundle\Repository\PartnerRepositoryInterface;
use Spipu\ApiPartnerBundle\Entity\PartnerInterface;

class MyPartnerRepository implements PartnerRepositoryInterface
{
    public function __construct(private ApiPartnerDoctrineRepository $repo) {}

    public function getAllPartners(): array
    {
        return $this->repo->findAll();
    }

    public function getPartnerByApiKey(string $apiKey): ?PartnerInterface
    {
        return $this->repo->findOneBy(['apiKey' => $apiKey, 'active' => true]);
    }

    public function getPartnerById(int $id): ?PartnerInterface
    {
        return $this->repo->find($id);
    }
}
```

Register it as a service (autowired by interface — no tag required):

```yaml
App\Api\MyPartnerRepository: ~
```

### 5. Implement RequestSecurityServiceInterface

This service validates the incoming request (signature, timestamp, etc.) and decides which routes a given partner is allowed to call:

```php
use Spipu\ApiPartnerBundle\Service\RequestSecurityServiceInterface;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Entity\PartnerInterface;
use Spipu\ApiPartnerBundle\Model\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class MyRequestSecurity implements RequestSecurityServiceInterface
{
    public function validate(Request $request, SymfonyRequest $symfonyRequest): void
    {
        // Validate the request (e.g., check HMAC signature, timestamp replay protection)
        // Throw SecurityException if invalid
    }

    public function isRouteAllowed(RouteInterface $route, ?PartnerInterface $partner): bool
    {
        if ($partner === null || !$partner->isApiEnabled()) {
            return false;
        }
        // e.g., check that the partner has access to this route code
        return true;
    }
}
```

Register it as a service (autowired by interface — no tag required):

```yaml
App\Api\MyRequestSecurity: ~
```

### 6. Register your API route classes

Routes are collected via the `spipu.api-partner.route` tag:

```yaml
App\Api\Route\:
    resource: '../src/Api/Route/'
    tags:
        - { name: spipu.api-partner.route }
```

## Admin Log Viewer

API request logs are available at `/admin/api-partner/log/`. See [API Logs](./logs.md).

[back](./README.md)
