# Интеграция с DummyJSON API

## Установка

1. Клонировать репозиторий
2. Установить зависимости:
```bash
composer install
```
3. Скопировать .env.example в .env
4. Внести переменные базы данных и для dummy в .env:
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dummy
DB_USERNAME=
DB_PASSWORD=

DUMMY_JSON_API_URL=https://dummyjson.com
DUMMY_JSON_USERNAME=kminchelle
DUMMY_JSON_PASSWORD=0lelplR
```
5. Запустить docker-compose:
```bash
docker-compose up -d
```
5. Запустить миграции:
```bash
php artisan migrate
```
6. Запустить сервер:
```bash
php artisan serve
```

## API

- `GET /api/products` - Получить все сохраненные товары из dummy
- `POST /api/products` - Добавить новый товар в dummy

## Импорт данных из dummy

```bash
# импортирует все iPhone товары
php artisan app:import-products
```
