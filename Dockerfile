FROM php:8.2-apache

# Installer les extensions MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier les fichiers du projet
COPY . /var/www/html/

# Définir le DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

# Autoriser .htaccess
RUN echo '<Directory /var/www/html/>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

# Créer le dossier uploads
RUN mkdir -p /var/www/html/uploads
RUN chmod 755 /var/www/html/uploads

# Exposer le port 80
EXPOSE 80

# Script d'initialisation de la base de données
COPY init-db.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/init-db.sh

CMD ["apache2-foreground"]
