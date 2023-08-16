# Выберите базовый образ.
FROM php:8.1-fpm

# Установите расширения PHP, которые вам необходимы
RUN docker-php-ext-install pdo_mysql

# Установите Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Определите рабочую директорию
WORKDIR /var/www/colnyshko
