FROM php:8.3-cli

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Application directory
WORKDIR /app

# Copy project files
COPY . /app

# Render will provide PORT.
# 10000 is used as a fallback for local Docker.
EXPOSE 10000

# Start PHP built-in web server
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t /app/public"]
