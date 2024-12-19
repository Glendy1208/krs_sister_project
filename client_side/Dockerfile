# Gunakan image PHP dengan Nginx
FROM php:8.1-fpm

# Install dependensi yang diperlukan
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && apt-get clean

# Set direktori kerja
WORKDIR /var/www/html

# Salin file proyek ke dalam container
COPY . /var/www/html

# Atur konfigurasi untuk Nginx
COPY ./default.conf /etc/nginx/sites-available/default

# Set environment variable untuk PHP
ENV PHP_MEMORY_LIMIT=256M

# Expose port untuk Nginx
EXPOSE 80

# Jalankan PHP-FPM dan Nginx dalam satu container
CMD service nginx start && php-fpm
