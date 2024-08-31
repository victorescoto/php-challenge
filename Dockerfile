FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libxml2-dev \
    uuid-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install uuid \
    && docker-php-ext-enable uuid

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html/

RUN composer install

CMD ["apache2-foreground"]

EXPOSE 80
