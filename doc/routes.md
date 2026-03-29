# Creating API Routes

[back](./README.md)

## Overview

An API route is defined by a class implementing two interfaces:

- **`RouteInterface`** — describes the route: code, URL pattern (regex), HTTP method, parameters, and the action service to call
- **`ActionInterface`** — implements the business logic, receives a `Context` object

A single class can implement both.

## RouteInterface Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `getCode()` | `string` | Unique route identifier |
| `getHttpMethod()` | `string` | `GET`, `POST`, `PUT`, `DELETE`, etc. |
| `getRoutePattern()` | `string` | Regex pattern matched against the URL path |
| `getActionServiceName()` | `string` | FQCN of the `ActionInterface` service to execute |
| `getPathParameters()` | `ParameterInterface[]` | Parameters extracted from the URL path |
| `getQueryParameters()` | `ParameterInterface[]` | Parameters from the query string |
| `getBodyParameters()` | `ParameterInterface[]` | Parameters from the request body |
| `getDescription()` | `?string` | Human-readable description (for Swagger) |
| `isDeprecated()` | `bool` | Whether this route is deprecated |
| `getResponseFormat()` | `?ResponseFormat` | Optional response structure for validation |

## Creating an Endpoint

```php
namespace App\Api\Route;

use Spipu\ApiPartnerBundle\Api\ActionInterface;
use Spipu\ApiPartnerBundle\Api\RouteInterface;
use Spipu\ApiPartnerBundle\Model\Context;
use Spipu\ApiPartnerBundle\Model\Response;
use Spipu\ApiPartnerBundle\Model\Parameter\IntegerParameter;
use Spipu\ApiPartnerBundle\Model\Parameter\StringParameter;

class GetProductRoute implements RouteInterface, ActionInterface
{
    public function __construct(private ProductRepository $products) {}

    // RouteInterface

    public function getCode(): string
    {
        return 'get_product';
    }

    public function getHttpMethod(): string
    {
        return 'GET';
    }

    public function getRoutePattern(): string
    {
        // Regex: matches /products/123
        return '/products/([0-9]+)';
    }

    public function getPathParameters(): array
    {
        return [
            new IntegerParameter('id', true, 'Product ID'),
        ];
    }

    public function getQueryParameters(): array
    {
        return [
            new StringParameter('locale', false, 'Response locale'),
        ];
    }

    public function getBodyParameters(): array
    {
        return [];
    }

    public function getActionServiceName(): string
    {
        return self::class;
    }

    public function getDescription(): ?string
    {
        return 'Get a product by ID';
    }

    public function isDeprecated(): bool
    {
        return false;
    }

    public function getResponseFormat(): ?ResponseFormat
    {
        return null;
    }

    // ActionInterface

    public function execute(Context $context): Response
    {
        $id     = $context->getRequest()->getPathParam('id');
        $locale = $context->getRequest()->getQueryParam('locale', 'en');

        $product = $this->products->find($id);
        if (!$product) {
            $response = new Response();
            $response->setCode(404)->setContentText('Product not found');
            return $response;
        }

        $response = new Response();
        $response->setCode(200)->setContentJson([
            'id'     => $product->getId(),
            'name'   => $product->getName($locale),
            'price'  => $product->getPrice(),
        ]);
        return $response;
    }
}
```

## Available Parameter Types

| Class | Description |
|-------|-------------|
| `StringParameter` | String value |
| `IntegerParameter` | Integer value |
| `NumberParameter` | Numeric (float) value |
| `BooleanParameter` | Boolean value |
| `DateParameter` | Date (Y-m-d) |
| `DateTimeParameter` | Datetime |
| `ArrayParameter` | JSON array |
| `ObjectParameter` | JSON object |
| `Generic\StringCommaSeparatedParameter` | Comma-separated string list |
| `Generic\IntCommaSeparatedParameter` | Comma-separated integer list |
| `Generic\FileParameter` | File upload |
| `Page\MaxParameter` | Pagination: max items per page |
| `Page\OffsetParameter` | Pagination: offset |

## Registering Routes

Tag the service with `spipu.api_partner.route`:

```yaml
# config/services.yaml
App\Api\Route\:
    resource: '../src/Api/Route/'
    tags:
        - { name: spipu.api_partner.route }
```

[back](./README.md)
