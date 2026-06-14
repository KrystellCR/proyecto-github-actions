# Imagen base oficial de PHP 8.3 en modo CLI (Command Line Interface).
FROM php:8.3-cli

# Define el directorio de trabajo dentro del contenedor.
# Todos los comandos posteriores se ejecutarán desde aquí.
WORKDIR /var/www/html

# Actualiza el índice de paquetes de Debian e instala:
# - git: para descargar dependencias desde repositorios.
# - unzip: Composer lo usa para extraer paquetes.
# - libsqlite3-dev: librerías necesarias para compilar SQLite.
#
# Luego instala las extensiones de PHP:
# - pdo: capa de abstracción para bases de datos.
# - pdo_sqlite: soporte para SQLite mediante PDO.
RUN apt-get update && apt-get install -y \
    git unzip libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Copia el ejecutable de Composer desde la imagen oficial de Composer.
# Esto aprovecha una construcción multi-stage para no instalar Composer manualmente.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia todos los archivos del proyecto al contenedor.
# Equivale a copiar el contenido del directorio actual.
COPY . .

# Instala las dependencias definidas en composer.json.
#
# --no-dev          -> omite dependencias de desarrollo.
# --no-interaction  -> evita preguntas durante la instalación.
# --prefer-dist     -> descarga paquetes empaquetados en lugar de clonar repositorios Git.
RUN composer install --no-dev --no-interaction --prefer-dist

# Documenta que la aplicación utilizará el puerto 8080.
# No publica el puerto automáticamente; solo sirve como referencia.
EXPOSE 8080

# Comando que se ejecutará cuando el contenedor inicie.
# Ejecuta el script start.sh ubicado en la carpeta docker.
CMD ["sh", "docker/start.sh"]