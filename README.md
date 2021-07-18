# pocket-youtube

Simple webpage for hosting video library via HTTP

## Prerequisites

You will need the following tools, commonly referred to as LAMP.

- Linux
- Apache
- MySQL
- PHP :vomiting_face:

> "This is the weapon of a web engineer. Not as clumsy or as random as Node.js. An elegant stack for a more civilized age." - *Ben Kenobi*

## Apache Configuration

The first step is to host your video library on Apache, which can be configured to permit access to specific directories outside the document root. Add the following to `/etc/apache2/apache2.conf`.

```apache
Alias "/videos" "/path/to/video/library"
<Directory /path/to/video/library>
    Options +Indexes
    Require all granted
</Directory>
```

Restart Apache for your changes to take effect. Your videos should then be visible at [http://localhost/videos/](http://localhost/videos/).

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
