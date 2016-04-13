FROM ubuntu:16.04
MAINTAINER Artur Luik <artur.luik@gmail.com>

EXPOSE 80

# Install dependencies
RUN apt-get update
RUN apt-get -y install apache2 php7.0 php7.0-pgsql

# Apache configuration
COPY config/apache/plagiarism-vhost.conf /etc/apache2/sites-available
RUN a2dissite 000-default.conf
RUN a2ensite plagiarism-vhost.conf

COPY config/init.sh .
RUN  chmod +x init.sh
CMD  ./init.sh

