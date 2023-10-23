FROM php:8.2-apache
    WORKDIR /var/www/html
        RUN chmod 777 .
        RUN apt-get update
        RUN apt-get upgrade -y
        RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql
        RUN docker-php-ext-enable pdo pdo_mysql
        # https://stackoverflow.com/a/48955845/13680015
        RUN a2enmod rewrite
