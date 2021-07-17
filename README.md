# pocket-youtube

Simple webpage for hosting video library via HTTP

## HTTP Server Configuration

I use LAMP (Linux + Apache + MySQL + PHP) because it gets the job done. Even though PHP is awful, it beats dealing with Node.js and npm. Make sure you have LAMP installed if you want to try out this project. In principle, this should also work with HTTP servers besides Apache. You could also use a non-Linux operating system, but why would you ever do that? :penguin:

The first step is to host your video library on Apache. Moving your entire video library into `/var/www/html` would be inconvenient and unnecessary. Apache can be configured to permit access to specific directories outside the document root. Add the following to `/etc/apache2/apache2.conf`.

```apache
Alias "/videos" "/path/to/video/library"
<Directory /path/to/video/library>
    Options +Indexes
    Require all granted
</Directory>
```

Restart Apache for your changes to take effect. Your video library should then be visible at [http://localhost/videos/](http://localhost/videos/).

```sh
service apache2 restart
```

A similar step follows for hosting the webpages in this repository at [http://localhost/youtube/](http://localhost/youtube/).

```apache
Alias "/youtube" "/path/to/this/repository"
<Directory /path/to/this/repository>
    Require all granted
</Directory>
```
