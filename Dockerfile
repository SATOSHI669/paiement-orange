FROM php:8.2-apache

# Installer les dépendances nécessaires pour l'extension curl
RUN apt-get update && apt-get install -y libcurl4-openssl-dev && rm -rf /var/lib/apt/lists/*

# Activer l'extension curl
RUN docker-php-ext-install curl

# Copier tous tes fichiers dans le serveur
COPY . /var/www/html/

# Configurer Apache pour accepter les requêtes
RUN a2enmod rewrite
