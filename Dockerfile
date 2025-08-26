# Menggunakan image resmi PHP 8.1 dengan FPM dan Alpine
FROM php:8.1-fpm-alpine

# Menginstal dependensi sistem yang diperlukan: git, mysql-client, dan ekstensi pdo_mysql
RUN apk add --no-cache \
    git \
    mysql-client \
    && docker-php-ext-install pdo_mysql

# Menginstal Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Menentukan direktori kerja di dalam kontainer
WORKDIR /var/www

# Salin file composer dan instal dependensi
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Salin semua kode aplikasi ke dalam direktori kerja
COPY . .

# Perintah untuk menjalankan server
# Railway akan secara otomatis memetakan PORT mereka ke port 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]