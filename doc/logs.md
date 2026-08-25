# API Logs

[back](./README.md)

## Overview

API requests are logged by the bundle into the `api_log_partner` database table (`ApiLogPartner` entity). With the default configuration every authenticated request is logged, whether it succeeded or failed. Once logging is narrowed down (see below), successful and fast requests are skipped so that the table only keeps what is worth reviewing.

## When Is a Log Created?

A request with an **empty API key is never logged**. Such a request is rejected before the partner is identified, and it leaves no trace in `api_log_partner`.

For every other request, a log entry is written as soon as **any** of these is true:

| Condition | Detail |
|-----------|--------|
| The response requires logging | HTTP code other than `200`, or forced by the route (see below) |
| Debug logging is enabled | `api.partner.log_debug` is `true` — this is the **default**, and it logs everything |
| The response format is invalid | Response format validation produced an error |
| The request was slow | Duration greater than or equal to `api.partner.log_slow_query` |

If none of them applies — a `200` response, returned quickly, with a valid format, and `log_debug` disabled — no log entry is created.

### Configuration

These settings are managed in the admin configuration screen.

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `api.partner.log_debug` | boolean | `true` | Log every request. Disable it in production to keep only errors and slow requests |
| `api.partner.log_slow_query` | integer (seconds) | `30` | Requests taking at least this long are always logged |
| `api.partner.validate_response_format` | boolean | `false` | Enable response format validation; a validation error always triggers a log |

### Forcing or Skipping the Log From a Route

`Response` derives the decision from its HTTP code, and a route can override it with `setLogNeeded()`:

```php
// Always log this response, even though it is a 200
$response = new Response();
$response->setCode(200)->setContentJson($data);
$response->setLogNeeded(true);

// Never log this expected 404
$response = new Response();
$response->setCode(404)->setContentText('Not found');
$response->setLogNeeded(false);
```

Call `setLogNeeded()` **after** `setCode()`: `setCode()` recomputes the flag from the new code and overwrites any previous override.

An override only removes the response from the decision. The three other conditions above still apply — with `log_debug` enabled, or on a slow request, a response marked `setLogNeeded(false)` is logged anyway.

## Log Entry Fields

| Field | Description |
|-------|-------------|
| `id` | Auto-incremented log ID |
| `partnerId` | ID of the authenticated partner (nullable if authentication failed) |
| `date` | Request timestamp |
| `userIp` | Client IP address |
| `userAgent` | Client User-Agent string |
| `apiKey` | API key used in the request |
| `requestTime` | Unix timestamp from the request (for replay detection) |
| `requestHash` | Request signature hash |
| `method` | HTTP method (`GET`, `POST`, etc.) |
| `route` | Raw URL path |
| `routeCode` | Matched route code (nullable if no route matched) |
| `queryString` | Query string parameters (up to 1 MB) |
| `bodyString` | Request body (up to 1 MB) |
| `responseStatus` | `success` or `error` |
| `responseCode` | HTTP response status code |
| `responseType` | Response Content-Type |
| `responseContent` | Response body (up to 1 MB) |
| `memoryUsage` | PHP memory used during the request (bytes) |
| `duration` | Request execution time (seconds) |

## Admin Log Viewer

The log grid is available at `/admin/api-partner/log/`. It allows:

- **Filtering** by partner, status, response code, route code, IP, date range, duration range, memory range
- **Sorting** by any column
- **Personalization** — choose which columns to display
- **Viewing the full response content** in a formatted view (JSON is pretty-printed)

[back](./README.md)
