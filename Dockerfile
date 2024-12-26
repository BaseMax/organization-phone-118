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
EXPOSE 8080

RUN ls -al /app
RUN ls -al /app/data

CMD ["sh", "-c", "bash run.sh"]

HEALTHCHECK CMD curl --fail http://localhost:8000 || curl --fail http://localhost:8080 || exit 1
