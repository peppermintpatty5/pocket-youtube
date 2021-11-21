#!/bin/sh

if [ $(id -u) -ne 0 ]; then
    echo "$0: must be root"
    exit 1
fi

# generate Apache config file from template
envsubst \
    < template/pocket-youtube.conf \
    > /etc/apache2/sites-available/pocket-youtube.conf
a2ensite pocket-youtube
systemctl reload apache2
