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

COPY config/init.sh .
COPY config/env.sh .
RUN  chmod +x init.sh
CMD  ./init.sh

