#!/bin/bash

# Load environment configuration
source env.sh


# Generate documentation
cd /tmp
apidoc -i /plagiarism/api/ -o /var/www/plagiarism/documentation -f "index\\.php$"

# Start services
service rabbitmq-server start
rabbitmqctl add_user $RABBIT_USER $RABBIT_PASSWORD
rabbitmqctl set_permissions -p / $RABBIT_USER ".*" ".*" ".*"
rabbitmqctl set_user_tags $RABBIT_USER administrator

# Application environment setup
rm -rf /var/www/html & mkdir /var/www/plagiarism
ln -s /plagiarism/web /var/www/plagiarism/web
ln -s /plagiarism/api /var/www/plagiarism/api
php /plagiarism/api/bin/start_workers.php

/usr/sbin/apache2ctl -D FOREGROUND
