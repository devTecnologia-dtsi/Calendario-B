# Imagen base oficial con Apache
FROM php:8.3-apache
 
# Instala extensiones necesarias y limpia caché
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    curl \
    vim \
    && docker-php-ext-install mysqli zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
 
# Instala Composer de forma global
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer
 
# Copia el proyecto al contenedor
WORKDIR /var/www
COPY ./html/public/ /var/www/html/public/
COPY ./src/ /var/www/src/
COPY ./config/ /var/www/config/
COPY ./config/conexion.php /var/www/connections/
COPY ./composer.json /var/www/composer.json
 
# Instala dependencias del backend
RUN composer install
RUN composer update
 
# Configuración PHP para archivos grandes
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i 's/post_max_size = .*/post_max_size = 10M/' /usr/local/etc/php/php.ini \
    && sed -i 's/upload_max_filesize = .*/upload_max_filesize = 10M/' /usr/local/etc/php/php.ini
 
# Ajustes de Apache
RUN a2enmod rewrite headers
 
RUN echo 'DB_HOST=10.0.20.189\n\
          DB_NAME=calendarios\n\
          DB_USER=root\n\
          DB_PASSWORD=Jailton81*\n\
          DB_PORT=3306\n\
          JWT_SECRET_KEY=SuperClaveUltraSecreta123!' > config/.env
 
# Puerto (8086) y VirtualHost
RUN echo 'ServerName localhost\n\
Listen 8086\n\
<VirtualHost *:8086>\n\
    DocumentRoot /var/www/html/public\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride None\n\
        Require all granted\n\
        RewriteEngine On\n\
        RewriteCond %{REQUEST_FILENAME} !-f\n\
        RewriteCond %{REQUEST_FILENAME} !-d\n\
        RewriteRule ^ index.php [L]\n\
    </Directory>\n\
    # Habilitar CORS
    <IfModule mod_headers.c>\n\
        Header set Access-Control-Allow-Origin "*"\n\
        Header set Access-Control-Allow-Methods "GET, POST, OPTIONS, PUT, DELETE"\n\
        Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"\n\
    </IfModule>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
 
# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data /var/www/src \
    && chown -R www-data:www-data /var/www/config
 
# puerto de exposición
EXPOSE 8086
 
# Comando por defecto
CMD ["apache2-foreground"]
 