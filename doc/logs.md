# API Logs

[back](./README.md)

## Overview

Every API request is automatically logged by the bundle into the `api_log_partner` database table (`ApiLogPartner` entity). No configuration is needed — logging happens transparently after each request, whether it succeeded or failed.

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
