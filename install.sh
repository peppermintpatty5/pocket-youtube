#!/bin/bash

if [ $EUID -ne 0 ]; then
    echo "$0: must be root"
    exit 1
fi

if [ -d videos ]; then
    envsubst \
        < server/local-youtube.conf \
        > /etc/apache2/sites-available/local-youtube.conf
    a2ensite local-youtube
    systemctl reload apache2
else
    echo "$0: missing directory 'videos'"
fi
