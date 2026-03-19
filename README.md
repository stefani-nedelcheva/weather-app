# Weather Service App

Laravel-based weather service with:

- API endpoint: `GET /api/weather?city=Sofia`
- Trend output based on current temperature vs average from last 10 days
- File-based caching and lock to minimize repeated third-party calls

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+

## Setup

```bash
git clone https://github.com/stefani-nedelcheva/weather-app.git
cd weather-app
cp .env.example .env
composer setup
```

## Run

```bash
composer dev
```

The app runs at `http://localhost:8000` with Vite HMR on port 5173.

## Access

- UI: `http://localhost:8000/weather`
- API: `http://localhost:8000/api/weather?city=Sofia`

## Tests

```bash
composer test
```
