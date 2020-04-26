FROM php:7.4-fpm

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update

RUN apt-get install -y zlib1g-dev libpq-dev git libicu-dev libxml2-dev librabbitmq-dev libssh-dev libzip-dev \
    libcurl4-gnutls-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libonig-dev unzip libxslt-dev libxslt1.1

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-install zip xml bcmath sockets curl mbstring xsl

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN pecl install amqp \
    && docker-php-ext-enable amqp

RUN pecl install igbinary \
    && docker-php-ext-enable igbinary

RUN pecl install redis \
#    && docker-php-ext-configure redis --enable-redis-igbinary \
    && docker-php-ext-enable redis

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer self-update --stable

RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;
RUN echo 'session.save_path = "/tmp"' >> /usr/local/etc/php/conf.d/docker-php-session.ini;

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Warsaw /etc/localtime
RUN "date"

WORKDIR /var/www/symfony