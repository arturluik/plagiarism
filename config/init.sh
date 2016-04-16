#!/bin/bash

# Load environment configuration
source env.sh

# Build API
if [ ! -d /var/www/html/api ]; then
    ln -s /plagiarism/api/ /var/www/html/api
fi

# Start services
service rabbitmq-server start
rabbitmqctl add_user $RABBIT_USER $RABBIT_PASSWORD
rabbitmqctl set_permissions -p / $RABBIT_USER ".*" ".*" ".*"
rabbitmqctl set_user_tags $RABBIT_USER administrator

/usr/sbin/apache2ctl -D FOREGROUND
