FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    curl \
    git \
    unzip \
    && docker-php-ext-install curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

RUN ls -al /app
RUN ls -al /app/data

CMD ["sh", "-c", "pwd && ls -al && ls -al data && cd data && php import.php && cd .. && php -S 0.0.0.0:8000 -t . api.php"]

HEALTHCHECK CMD curl --fail http://localhost:8000 || exit 1
