FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       unzip \
       libpq-dev \
       libzip-dev \
       libicu-dev \
       libxml2-dev \
    && docker-php-ext-install \
       pdo \
       pdo_pgsql \
       pgsql \
       bcmath \
       intl \
       xml \
       dom \
       xmlwriter \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

RUN mkdir -p /tmp && chmod 1777 /tmp

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
