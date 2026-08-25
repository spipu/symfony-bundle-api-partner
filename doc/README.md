# Spipu API Partner Bundle

The **ApiPartnerBundle** provides a REST API framework for secure partner integrations. Partners authenticate using an API key, and requests are logged according to a configurable policy. API routes are PHP service classes. Authorization logic (which routes a partner can call) is delegated to the application.

## Documentation

- [Installation](./install.md)
- [Creating API Routes](./routes.md)
- [Partner Integration](./partners.md)
- [API Logs](./logs.md)

## Features

- **Route registration** — API endpoints are PHP services implementing `RouteInterface` + `ActionInterface`
- **Partner authentication** — partners are identified by an `apiKey`; the application provides the lookup via `PartnerRepositoryInterface`
- **Request security** — the application controls authorization via `RequestSecurityServiceInterface`
- **Typed parameters** — path, query, and body parameters are declared and validated per route
- **Response format validation** — optional validation of the response structure
- **Configurable API logging** — requests are logged to `ApiLogPartner` with partner, IP, method, route, status, response code, memory, and duration; errors, slow requests, and format failures are always kept, and a route can force or skip its own log
- **Admin log viewer** — filterable/sortable grid of API logs at `/admin/api-partner/log/`
- **Swagger/OpenAPI** — documentation generation via `AbstractApiDocumentationService`

## Requirements

- PHP 8.1+
- Symfony 6.4+
- `spipu/core-bundle`
- `spipu/ui-bundle`
- `spipu/configuration-bundle`
- Doctrine ORM

## Architecture

```
HTTP Request
  └── ApiControllerService: extract apiKey → PartnerRepositoryInterface::getPartnerByApiKey()
        └── RequestSecurityServiceInterface: validate() + isRouteAllowed()
              └── RouteService: match URL+method to RouteInterface (by regex pattern)
                    └── ContextService: parse & validate parameters
                          └── ActionInterface::execute(Context)
                                └── LoggerService: save ApiLogPartner
```
