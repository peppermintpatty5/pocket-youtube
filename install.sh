#!/bin/bash

if [ -L videos ]; then
    video_dir=$(realpath videos)

    if [ -d "$video_dir" ]; then
        export video_dir

        envsubst < server/local-youtube.conf |
            tee /etc/apache2/conf-available/local-youtube.conf
        a2enconf local-youtube
        systemctl reload apache2
    else
        echo "$0: 'videos' must refer to a directory"
    fi
else
    echo "$0: missing symlink 'videos'"
fi
