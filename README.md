# Weather Service App

Laravel-based weather service with:

- API endpoint: `GET /api/weather?city=Sofia`
- Trend output based on current temperature vs average from last 10 days
- Redis caching and lock to minimize repeated third-party calls
- Docker-first local setup

## Prerequisites

- Docker + Docker Compose plugin
- Git

No local PHP/MySQL/Redis installation is required.

## 1) Clone the project

```bash
git clone https://github.com/stefani-nedelcheva/weather-app.git
cd weather-app
```

## 2) Create `.env`

```bash
cp .env.example .env
```

Set these values in `.env`:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_PORT=8080

WWWUSER=1000
WWWGROUP=1000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=weather_service
DB_USERNAME=sail
DB_PASSWORD=password

CACHE_STORE=redis
REDIS_HOST=redis
REDIS_PORT=6379

SESSION_DRIVER=database
```

## 3) Install dependencies in containerized way

This is needed before starting containers because `compose.yaml` builds the app image from `./vendor/laravel/sail/runtimes/...`.
If `vendor` does not exist yet, Docker cannot find that build path.

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app composer:2 install
```

## 4) Start containers

Option A (Docker Compose):

```bash
docker compose up -d --build
docker compose ps
```

Option B (Laravel Sail wrapper, equivalent):

```bash
./vendor/bin/sail up -d
./vendor/bin/sail ps
```

## 5) Migrations

Using Docker Compose:

```bash
docker compose exec laravel.test php artisan migrate
```

Using Sail:

```bash
./vendor/bin/sail artisan migrate
```

## 6) Access app and API

- UI: `http://localhost:8080/weather`
- API: `http://localhost:8080/api/weather?city=Sofia`

## 7) Run tests (PHPUnit)

Using Docker Compose:

```bash
docker compose exec laravel.test php artisan test
```

Using Sail:

```bash
./vendor/bin/sail artisan test
```

## Optional: SQL-only database/table creation

If you want to manually create only the weather table:

```bash
docker compose exec mysql mysql -uroot -p"${DB_PASSWORD}" -e "
CREATE DATABASE IF NOT EXISTS weather_service CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'sail'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON weather_service.* TO 'sail'@'%';
FLUSH PRIVILEGES;
USE weather_service;
CREATE TABLE IF NOT EXISTS weather (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  city VARCHAR(120) NOT NULL,
  temperature INT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY weather_city_created_at_unique (city, created_at),
  KEY weather_city_index (city),
  KEY weather_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
"
```
