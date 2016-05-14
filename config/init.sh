#!/bin/bash

# Load environment configuration
source env.sh


# Apidoc configuration (used to generate automated API documentation)
cp /config/apidoc.json /tmp/apidoc.json

# Apache configuration
cp /config/apache/plagiarism-vhost.conf /etc/apache2/sites-available
a2dissite 000-default.conf
a2ensite plagiarism-vhost.conf
a2enmod rewrite
a2enmod dump_io

# PHP configuration
cp /config/php/php.ini /etc/php/7.0/apache2/conf.d/30-application-config.ini
cp /config/php/php.ini /etc/php/7.0/cli/conf.d/30-application-config.ini

# Postgresql configuration
cp /config/postgresql/postgresql.conf /etc/postgresql/9.5/main/postgresql.conf
cp /config/postgresql/pg_hba.conf /etc/postgresql/9.5/main/pg_hba.conf

# RabbitMQ configuration
cp /config/rabbitmq/rabbitmq-env.conf /etc/rabbitmq/rabbitmq-env.conf
cp /config/rabbitmq/rabbitmq.conf /etc/rabbitmq/rabbitmq.conf
service rabbitmq-server start && rabbitmq-plugins enable rabbitmq_management

# Generate documentation
cd /tmp
apidoc -i /plagiarism/api/ -o /var/www/plagiarism/documentation -f ".php$"

# Start services
service redis-server start
service rabbitmq-server start
rabbitmqctl add_user $RABBIT_USER $RABBIT_PASSWORD
rabbitmqctl set_permissions -p / $RABBIT_USER ".*" ".*" ".*"
rabbitmqctl set_user_tags $RABBIT_USER administrator


# Database setup
service postgresql start
if [ $(sudo -u postgres psql  -c "\l" | grep $POSTGRESQL_DB_NAME | wc -l) -eq 0 ]; then
    # New installation
    sudo -u postgres createdb plagiarism
    sudo -u postgres psql -c "CREATE USER $POSTGRESQL_USER WITH PASSWORD '$POSTGRESQL_PASSWORD';"
    sudo -u postgres psql -c "ALTER ROLE $POSTGRESQL_USER LOGIN";
    sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $POSTGRESQL_DB_NAME TO $POSTGRESQL_USER";
    echo "Postgresql database initialized"
fi

# Application environment setup
if [ ! -d /var/www/plagiarism/web ]; then
    rm -rf /var/www/html
    ln -s /plagiarism/web /var/www/plagiarism/web
    ln -s /plagiarism/api /var/www/plagiarism/api
    php /plagiarism/api/bin/start_workers.php
    echo "Application folder created"
fi

cd /plagiarism
./composer.phar install
./composer.phar dump-autoload -o
npm install
cd web
node ../node_modules/webpack/bin/webpack.js --progress --colors
cd ../api
./deps/bin/doctrine orm:schema-tool:drop --force
./deps/bin/doctrine orm:schema-tool:create
./deps/bin/doctrine orm:generate-proxies

/usr/sbin/apache2ctl -D FOREGROUND
