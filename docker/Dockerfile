FROM php:7.4-apache


RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip \
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug


COPY . /var/www/html/


RUN chmod -R 777 /var/www/html/runtime


COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite


COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer


RUN composer install --no-interaction


EXPOSE 80

CMD ["apache2-foreground"]