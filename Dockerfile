FROM ubuntu:16.04
MAINTAINER Artur Luik <artur.luik@gmail.com>

# Install dependencies
RUN apt-get update && apt-get -y install apache2 php7.0 php7.0-pgsql \
                        libapache2-mod-php7.0 vim npm nodejs \
                        php7.0-mbstring php7.0-bcmath php7.0-dom php7.0-zip git \
                        postgresql-9.5 php7.0-pgsql \
                        postgresql-client sudo wget \
                        redis-server php-redis \
                        openjdk-8-jre \
                        --fix-missing

RUN echo 'deb http://www.rabbitmq.com/debian/ testing main' | \
    tee /etc/apt/sources.list.d/rabbitmq.list

RUN wget -O- https://www.rabbitmq.com/rabbitmq-signing-key-public.asc | sudo apt-key add -

RUN apt-get update && apt-get -f -y install rabbitmq-server
RUN ln -s /usr/bin/nodejs /usr/bin/node

RUN npm install apidoc -g

# Apidoc configuration (used to generate automated API documentation)
COPY config/apidoc.json /tmp/apidoc.json

# Apache configuration
COPY config/apache/plagiarism-vhost.conf /etc/apache2/sites-available
RUN a2dissite 000-default.conf
RUN a2ensite plagiarism-vhost.conf
RUN a2enmod rewrite
RUN a2enmod dump_io

# PHP configuration
COPY config/php/php.ini /etc/php/7.0/apache2/conf.d/30-application-config.ini
COPY config/php/php.ini /etc/php/7.0/cli/conf.d/30-application-config.ini

# Postgresql configuration
COPY config/postgresql/postgresql.conf /etc/postgresql/9.5/main/postgresql.conf
COPY config/postgresql/pg_hba.conf /etc/postgresql/9.5/main/pg_hba.conf

# RabbitMQ configuration
RUN service rabbitmq-server start && rabbitmq-plugins enable rabbitmq_management
COPY config/rabbitmq/rabbitmq-env.conf /etc/rabbitmq/rabbitmq-env.conf
COPY config/rabbitmq/rabbitmq.conf /etc/rabbitmq/rabbitmq.conf

COPY config/init.sh .
COPY config/env.sh .
RUN  chmod +x init.sh
CMD  ./init.sh

