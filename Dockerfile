FROM php:8.3-apache

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache/fallback.conf /etc/apache2/conf-available/fallback.conf
RUN a2enconf fallback

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip zip \
 && rm -rf /var/lib/apt/lists/* \
 && docker-php-ext-install pdo_mysql

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

COPY ./ /var/www/html
RUN composer dump-autoload --optimize --no-dev

RUN chown -R www-data:www-data /var/www/html

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
