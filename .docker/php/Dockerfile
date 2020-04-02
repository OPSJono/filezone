#==================
# PHP Dependencies
#==================
FROM composer:1.9.3 as vendor

COPY database database/
COPY tests tests/

COPY composer.json composer.json
COPY composer.lock composer.lock
COPY vendor vendor

ARG github_api_token=none
ENV COMPOSER_AUTH {\"github-oauth\":{\"github.com\":\"$github_api_token\"}}

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#==================
# Application
#==================
FROM php:7.4-fpm

ARG xdebug=off

#Laravel dependencies
RUN apt-get update \
	&& apt-get install -y \
		openssl -y \
		libzip-dev \
        zip \
        nano \
        vim \
        watch \
		unzip \
		wget \
		build-essential \
		fontconfig \
		fontconfig-config \
		fonts-dejavu-core \
		fonts-droid-fallback \
		fonts-noto-mono \
		ghostscript \
		gsfonts \
		hicolor-icon-theme \
		libxt6 \
		libavahi-client3 \
		libavahi-common-data \
		libavahi-common3 \
		libcairo2 \
		libcups2 \
		libcupsfilters1 \
		libcupsimage2 \
		libdatrie1 \
		libdjvulibre-text \
		libdjvulibre21 \
		libfftw3-double3 \
		libfontconfig1 \
		libgraphite2-3 \
		libgs9 \
		libgs9-common \
		libharfbuzz0b \
		libijs-0.35 \
#		libilmbase12 \
		libjbig0 \
        libjpeg-dev \
		libjbig2dec0 \
		libjpeg62-turbo \
		libturbojpeg0 \
		liblcms2-2 \
		liblqr-1-0 \
		libnetpbm10 \
#		libopenexr22 \
		libpango-1.0-0 \
		libpangocairo-1.0-0 \
		libpangoft2-1.0-0 \
		libpaper-utils \
		libpaper1 \
		libpixman-1-0 \
		libthai-data \
		libthai0 \
		libtiff5 \
		libwmf0.2-7 \
		libxcb-render0 \
		libxcb-shm0 \
		libxrender1 \
		netpbm \
		poppler-data \
		libraw-bin \
		libonig-dev \
		bzip2 \
		ffmpeg \
		libfreetype6 \
		libfreetype6-dev

RUN pecl install redis-5.1.0
RUN docker-php-ext-enable redis

RUN docker-php-ext-configure zip \
    	&& docker-php-ext-install zip \
    		pdo_mysql \
    		bcmath \
    		mbstring \
    		ctype \
    		json \
    		gd \
    		pcntl

RUN if [ "x$xdebug" = "xon" ] ; \
    then  pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

#Depency install cleanup
RUN	apt-get autoremove -y \
    && apt-get autoclean -y \
    && apt-get clean -y \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#Config
COPY .docker/php/config/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY .docker/php/config/fpm.conf /usr/local/etc/php-fpm.d/fpm.conf

# Composer
COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer

# Create group "devuser"
# set group id "uid" (to match host)
ARG uid=1000
RUN groupadd -g $uid devuser
# Create user "devuser"
# set user id "uid" (to match host)
# assign to existing group id "uid" (to match host)
# set home directory "/home/devuser"
# set shell "/bin/bash"
RUN useradd -d /home/devuser -s /bin/bash -u $uid -g $uid devuser
# Create home directory
RUN mkdir /home/devuser
# User and group own home directory
RUN chown -R devuser:devuser /home/devuser

#App Code
COPY --chown=devuser:devuser . /var/www
COPY --from=vendor --chown=devuser:devuser /app/vendor/ /var/www/vendor/

ARG github_api_token=none
ENV COMPOSER_AUTH {\"github-oauth\":{\"github.com\":\"$github_api_token\"}}

USER devuser

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]
