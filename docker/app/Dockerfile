FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        openssl libssl-dev \
        libxml2-dev \
        unzip \
        libssl-dev \
        openssl \
        ssl-cert \
        git \
        libonig-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod ssl \
    && a2ensite default-ssl

RUN docker-php-ext-install -j$(nproc) iconv xml zip bcmath

RUN cd /etc/apache2/mods-enabled \
    && ln -s ../mods-available/rewrite.load

RUN cd /usr/local/bin \
    && curl -s http://getcomposer.org/installer | php \
    && mv composer.phar composer

WORKDIR /var/www/html
