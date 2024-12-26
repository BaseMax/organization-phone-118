FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    curl \
    && docker-php-ext-install curl

WORKDIR /var/www/html

COPY . .

EXPOSE 9000

CMD ["php-fpm"]

HEALTHCHECK CMD curl --fail http://localhost:9000 || exit 1