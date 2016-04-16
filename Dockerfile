FROM ubuntu:16.04
MAINTAINER Artur Luik <artur.luik@gmail.com>

EXPOSE 80

# Install dependencies
RUN apt-get update && apt-get -y install apache2 php7.0 php7.0-pgsql \
                        libapache2-mod-php7.0 vim \
                        --fix-missing

RUN echo 'deb http://www.rabbitmq.com/debian/ testing main' | \
        tee /etc/apt/sources.list.d/rabbitmq.list

RUN apt-get update & apt-get -f -y install rabbitmq-server

# Apache configuration
COPY config/apache/plagiarism-vhost.conf /etc/apache2/sites-available
RUN a2dissite 000-default.conf
RUN a2ensite plagiarism-vhost.conf

# RabbitMQ configuration
RUN service rabbitmq-server start && rabbitmq-plugins enable rabbitmq_management
COPY config/rabbitmq/rabbitmq-env.conf /etc/rabbitmq/rabbitmq-env.conf
COPY config/rabbitmq/rabbitmq.conf /etc/rabbitmq/rabbitmq.conf

COPY config/init.sh .
COPY config/env.sh .
RUN  chmod +x init.sh
CMD  ./init.sh

