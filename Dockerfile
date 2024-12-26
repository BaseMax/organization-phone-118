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

CMD ["sh", "-c", "sleep 5 && php data/import.php && if [ $? -eq 0 ]; then php -S 0.0.0.0:8080 -t /app index.html & php -S 0.0.0.0:8000 -t /app api.php; else echo 'Import script failed, exiting...'; exit 1; fi"]

HEALTHCHECK CMD curl --fail http://localhost:8000 || curl --fail http://localhost:8080 || exit 1
