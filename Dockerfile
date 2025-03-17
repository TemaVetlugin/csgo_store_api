FROM bitnami/php-fpm:8.2.5

ARG COMPOSER_VERSION=2.5.7

COPY .editorconfig .env artisan composer.json composer.lock package.json phpunit.xml vite.config.js /app/

# Install Cron
RUN apt-get update
RUN apt-get -y install cron
COPY crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab

# Install NPM
RUN apt-get -y install nodejs
RUN apt-get -y install npm

# Instal PM2
RUN npm install pm2 -g

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}

# Change PHP memory limit
RUN sed -i 's/memory_limit\s*=.*/memory_limit=4096M/g' /opt/bitnami/php/etc/php.ini

# Copy project files
COPY app /app/app
COPY bootstrap /app/bootstrap
COPY config /app/config
COPY database /app/database
COPY docs /app/docs
COPY public /app/public
COPY resources /app/resources
COPY routes /app/routes
COPY storage /app/storage
COPY tests /app/tests

WORKDIR /app

# Install dependencies
RUN composer install --no-scripts --no-progress --no-suggest --no-interaction --no-ansi --prefer-dist --quiet
