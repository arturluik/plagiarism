#!/bin/bash

# Build API
if [ ! -d /var/www/html/api ]; then
    ln -s /plagiarism/api/ /var/www/html/api
fi

/usr/sbin/apache2ctl -D FOREGROUND
