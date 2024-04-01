#!/bin/sh
# See Dockerfile

apt update
apt upgrade -y
apt install -y lsb-release gnupg2 ca-certificates apt-transport-https software-properties-common

# https://stackoverflow.com/a/75915264/13680015
add-apt-repository ppa:ondrej/php -y
apt update
apt upgrade -y

apt install php8.2
apt install php8.2-common

apt install -y libcurl4-openssl-dev
apt install -y zlib1g-dev
apt install -y libzip-dev
apt install -y libpng-dev
apt install -y libicu-dev
apt install -y libonig-dev
apt install -y libssl-dev
apt install -y libxml2-dev

# https://github.com/docker-library/php/issues/233#issuecomment-288727629
# docker-php-ext-install -j$(nproc) curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql zip opcache ; i() { cp "/usr/src/php/ext/$1/config0.m4" "/usr/src/php/ext/$1/config.m4"; }; i curl; i fileinfo; i gd; i intl; i mbstring; i mysqli; i openssl; i zip; i opcache ; docker-php-ext-install -j$(nproc) curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql zip opcache

# https://stackoverflow.com/a/40816033/13680015
i() { apt install -y php8.2-$1; }; i curl; i fileinfo; i gd; i intl; i mbstring; i mysqli; i openssl; i xml; i zip; i opcache;
phpenmod curl fileinfo gd intl mbstring mysqli openssl pdo pdo_mysql xml zip opcache

# https://stackoverflow.com/a/48955845/13680015
# a2enmod rewrite
