FROM php:8.2-apache

ENV TZ=America/Fortaleza

# Copia o código da aplicação para o container
COPY . /var/www/html

# Copia a configuração do apache
COPY ./docker/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf 

# Instala o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    nano \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    zip \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath opcache intl zip \
    && a2enmod rewrite \
    && /usr/bin/composer install \
    && chown -R www-data:www-data /var/www/html


# Expõe a porta 80
EXPOSE 80