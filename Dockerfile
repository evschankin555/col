# Выберите базовый образ.
FROM php:8.1-fpm

# Установите расширения PHP, которые вам необходимы
RUN docker-php-ext-install pdo_mysql

# Измените настройки php.ini
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/upload.ini
RUN echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/upload.ini

# Установите Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Определите рабочую директорию
WORKDIR /var/www/colnyshko
