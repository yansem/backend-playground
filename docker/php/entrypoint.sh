#!/bin/sh
set -e

cd /var/www

# Установка зависимостей
composer install --no-interaction --prefer-dist --optimize-autoloader

# Создание .env, если его нет
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Автонастройка .env под Postgres + Docker
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=5432/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=laravel/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=laravel/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=secret/' .env

# Генерация ключа
php artisan key:generate --force

# Миграции (не падаем, если БД ещё не готова)
php artisan migrate --force || true

# Права
chmod -R 775 storage bootstrap/cache
chmod -R 755 public

echo "✅ Laravel запущен (самая простая версия)"

exec "$@"
