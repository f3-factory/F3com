FROM php:8.2-apache

ARG DEBIAN_FRONTEND=noninteractive

# Update
RUN apt-get -y update --fix-missing && \
    apt-get upgrade -y && \
    apt-get --no-install-recommends install -y apt-utils && \
    rm -rf /var/lib/apt/lists/*

# Install tools and libaries
RUN apt-get -y update && \
	apt-get -y --no-install-recommends install --fix-missing \
        wget \
		dialog \
		locales \
		zlib1g-dev \
		libzip-dev \
		libicu-dev && \
	apt-get -y --no-install-recommends install --fix-missing apt-utils \
		build-essential \
		git \
		curl \
		libonig-dev && \
	apt-get -y --no-install-recommends install --fix-missing libcurl4 \
		libcurl4-openssl-dev \
		zip \
		openssl && \
	rm -rf /var/lib/apt/lists/* && \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the locales
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && \
    locale-gen

ENV LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    LC_ALL=en_US.UTF-8

RUN pecl channel-update pecl.php.net

# Other PHP extensions
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install gettext
RUN docker-php-ext-install exif
RUN docker-php-ext-install zip

# Install XDebug
RUN pecl install xdebug \
	&& docker-php-ext-enable xdebug
COPY docker-php-ext-xdebug.ini  /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Freetype
RUN apt-get -y update && \
    apt-get --no-install-recommends install -y libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Enable apache modules
RUN a2enmod rewrite headers ssl

# Cleanup
RUN rm -rf /usr/src/*

RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html
