FROM php:8.0-apache


RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev libmagickwand-dev --no-install-recommends

RUN pecl install redis-5.3.4 \
    && pecl install xdebug-3.0.4 \
    && pecl install psr \
    && docker-php-ext-enable redis xdebug psr

RUN docker-php-ext-install zip

# install composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Install PHPUnit
RUN curl -OL https://phar.phpunit.de/phpunit.phar && chmod 755 phpunit.phar && mv phpunit.phar /usr/local/bin/ && ln -s /usr/local/bin/phpunit.phar /usr/local/bin/phpunit


ADD docker/userphp.ini /usr/local/etc/php/conf.d/userphp.ini
ADD docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# RUN systemctl restart apache2

ADD ./ /var/www/html/

WORKDIR /var/www/html/


RUN composer install -n --prefer-dist

