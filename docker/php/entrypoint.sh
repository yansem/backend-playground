#!/bin/sh
set -e

cd /var/www

echo "Waiting for database..."
until pg_isready -h db -p 5432 -U laravel; do
  sleep 1
done

# Установка зависимостей
composer install --no-interaction --prefer-dist

# Создание .env
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Генерация ключа Laravel
php artisan key:generate

# Миграции (не падаем, если БД ещё не готова)
php artisan migrate --force || true

exec "$@"
