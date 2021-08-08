#!/bin/bash

if [ $USER != root ]; then
    echo "$0: must be root"
    exit 1
fi

if [ -d videos ]; then
    envsubst \
        < server/local-youtube.conf \
        > /etc/apache2/conf-available/local-youtube.conf
    a2enconf local-youtube
    systemctl reload apache2
else
    echo "$0: missing directory 'videos'"
fi
