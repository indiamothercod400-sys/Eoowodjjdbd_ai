FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y curl \
    && docker-php-ext-install \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . /app

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
