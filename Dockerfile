FROM php:8.2-apache
    WORKDIR /var/www/html
        RUN chmod 777 .
        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install -y libcurl4-openssl-dev
        RUN apt-get install -y zlib1g-dev
        RUN apt-get install -y libzip-dev
        RUN apt-get install -y libpng-dev
        RUN apt-get install -y libicu-dev
        RUN apt-get install -y libonig-dev
        RUN apt-get install -y libssl-dev
        # https://github.com/docker-library/php/issues/233#issuecomment-288727629
        RUN docker-php-ext-install -j$(nproc) curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql zip opcache ; i() { cp "/usr/src/php/ext/$1/config0.m4" "/usr/src/php/ext/$1/config.m4"; }; i curl; i fileinfo; i gd; i intl; i mbstring; i mysqli; i openssl; i zip; i opcache ; docker-php-ext-install -j$(nproc) curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql zip opcache
        RUN docker-php-ext-enable curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql zip opcache
        # https://stackoverflow.com/a/48955845/13680015
        RUN a2enmod rewrite
