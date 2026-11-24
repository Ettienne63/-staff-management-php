# Setup

## Runtime

PHP 8.5, Composer, Node 24 (matching CI in `.github/workflows/laravel.yml`).

## Fresh local checkout

```bash
git switch {{BRANCH}}
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

## Verify

```bash
vendor/bin/pint --test
php artisan test
npm run build
```
