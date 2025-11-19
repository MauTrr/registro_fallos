# Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias necesarias para compilar extensiones y limpiar cache
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    pkg-config \
    libssl-dev \
    libcurl4-openssl-dev \
    libz-dev \
    build-essential \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache (habilitar mod_rewrite)
RUN a2enmod rewrite

# Copiar configuración personalizada de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copiar tu proyecto al contenedor
COPY . /var/www/html/

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias de Composer en modo producción
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Ajustar permisos (seguridad)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Definir variable de entorno para conexión a MongoDB (se sobreescribe en Render)
ENV MONGO_URI="mongodb+srv://usuario:password@cluster0.mongodb.net/?appName=Cluster0"

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
