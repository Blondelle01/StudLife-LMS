FROM php:8.2-apache

# Installation des extensions PDO pour MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activation du module de réécriture d'Apache
RUN a2enmod rewrite

# Copie des fichiers du projet dans le serveur
COPY . /var/www/html/

# Configuration des permissions pour Apache
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80