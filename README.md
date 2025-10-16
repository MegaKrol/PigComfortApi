## Pig Farm Advisory API (PHP 8.2)

Pig-farm themed API that proxies Open-Meteo and returns hourly conditions with a simple comfort index and management advisories. Supports filters and pagination.

### Features

- PHP 8.2+, Composer autoloading
- `.env` configuration via `vlucas/phpdotenv`
- HTTP client via `guzzlehttp/guzzle`
- Minimal router and controllers
- JSON responses, pagination, and filters

### Setup

1. Install PHP 8.2+ and Composer.
2. From project root, install dependencies:

```bash
composer install
```

3. Create `.env` (auto-copies from `.env.example` after autoload dump). Adjust values if needed.

### Run (Built-in server)

```bash
composer start
```

Serves on `http://localhost:8000` by default.

### Endpoints

- `GET /health` → basic status
- `GET /api/conditions` → list hourly pig-farm conditions from Open-Meteo
  - Query params:
    - `lat` (float): latitude (default from env)
    - `lon` (float): longitude (default from env)
    - `days` (int): days ahead, 1-7 (default from env)
    - `page` (int): page number (default 1)
    - `perPage` (int): items per page (default 24, max 72)
    - Filters applied to transformed data:
      - `minTempC` (float)
      - `maxTempC` (float)
      - `minComfortIndex` (int)
      - `maxComfortIndex` (int)

### Example requests

```bash
curl "http://localhost:8000/api/conditions?lat=52.52&lon=13.405&days=2&minComfortIndex=40&page=1&perPage=12"

curl "http://localhost:8000/api/conditions?minTempC=10&maxTempC=25&maxComfortIndex=80&page=2&perPage=24"
```

### Notes

- Data source: `https://open-meteo.com/` (public, no key required)
- Comfort index is a simple heuristic for pigs based on temperature and humidity.

### Inspiration

- Structure inspired by the Asian Store API pattern: `SWT_STORE` [`github.com/meuans/SWT_STORE`](https://github.com/meuans/SWT_STORE)
