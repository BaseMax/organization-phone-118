FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    curl \
    && docker-php-ext-install curl

WORKDIR /var/www/html

COPY . .

EXPOSE 8000

CMD ["sh", "-c", "php data/import.php && php -S 0.0.0.0:8000 -t . api.php"]

HEALTHCHECK CMD curl --fail http://localhost:8000 || exit 1
