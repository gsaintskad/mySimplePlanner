FROM php:8.5-rc-fpm

WORKDIR /var/www/html

# 1. Zainstaluj zależności systemowe
RUN apt-get update && apt-get install -y \
    git \
    unzip \
   libzip-dev

# 2. Zainstaluj rozszerzenia PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# 3. Zainstaluj Composera
COPY --from=composer /usr/bin/composer /usr/bin/composer

# 4. Skopiuj pliki zależności
# Kopiujemy je osobno, aby wykorzystać cache Dockera.
# Docker nie będzie ponownie instalował zależności, jeśli te pliki się nie zmienią.
COPY composer.json composer.lock ./

# 5. Zainstaluj zależności Composera
# tworzy folder /var/www/html/vendor wewnątrz obrazu
RUN composer install --no-dev --optimize-autoloader

# 6. Skopiuj resztę aplikacji
COPY . .