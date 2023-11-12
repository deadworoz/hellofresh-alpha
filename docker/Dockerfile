FROM php:8.2-cli-bookworm

RUN apt-get update && \
    apt-get install --no-install-recommends --yes \
    # unzip is used by composer install --prefer-dist
    unzip && \
    apt-get clean

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /app

WORKDIR /app
ENV COMPOSER_CACHE_DIR=/app/.composer/cache
