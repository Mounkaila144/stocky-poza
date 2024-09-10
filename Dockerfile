# Utiliser l'image officielle PHP avec FPM
FROM php:8.1-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    python-is-python3

# Installer les extensions PHP requises
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip calendar

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
RUN composer config --global process-timeout 3600
RUN composer global require "laravel/installer"

# Copier tous les fichiers du projet
COPY . /var/www

# Installer les dépendances du projet PHP
RUN composer install

# Installer les dépendances npm
#RUN npm install

# Donner les permissions correctes
RUN chown -R www-data:www-data /var/www \
    && chmod -R 777 /var/www/storage

# Exposer le port 9000 et lancer PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
