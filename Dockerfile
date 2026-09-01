FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    icu-dev \
    nginx

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd intl

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Permissions for web server
RUN chown -R www-data:www-data /var/www/html/public /var/www/html/storage 2>/dev/null || true

EXPOSE 9000
CMD ["php-fpm"]
