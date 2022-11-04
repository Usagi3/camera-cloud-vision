FROM php:7.3-apache

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
        nodejs \
        npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod ssl \
    && a2ensite default-ssl \
    && docker-php-ext-install -j$(nproc) iconv mysqli mbstring xml tokenizer zip bcmath\
    && pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

RUN cd /etc/apache2/mods-enabled \
    && ln -s ../mods-available/rewrite.load

WORKDIR /var/www/html
