FROM php:7.0-fpm

# install OS software
RUN apt-get update && apt-get install -y \
    python-setuptools \
    python-software-properties \
    software-properties-common \
    wget \
    git \
    curl

# Add packages for PHP 7
RUN echo "deb http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/dotdeb.list
RUN wget https://www.dotdeb.org/dotdeb.gpg && apt-key add dotdeb.gpg
RUN apt-get update -y

# Install PHP 7 libraries
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    php7.0-zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
RUN "date"

# Install extensions
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/publisher
