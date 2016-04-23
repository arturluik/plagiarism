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
cd api
./deps/bin/doctrine orm:schema-tool:drop --force
./deps/bin/doctrine orm:schema-tool:create
./deps/bin/doctrine orm:generate-proxies

/usr/sbin/apache2ctl -D FOREGROUND
