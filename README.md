# pocket-youtube

Simple webpage for hosting video library via HTTP

## Installation

You will need the following tools, commonly referred to as LAMP.

- Linux
- Apache
- MySQL
- PHP :persevere:

> "This is the weapon of a web engineer. Not as clumsy or as random as Node.js. An elegant stack for a more civilized age." - *Ben Kenobi*

### Apache Configuration

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

### Database Setup

This project requires `videos/` to be in a specific format. All files must be named by their YouTube ID, which guarantees unique and simple filenames. Metadata and thumbnails for each video are also needed.

```txt
videos/
├── dQw4w9WgXcQ.info.json
├── dQw4w9WgXcQ.mp4
└── dQw4w9WgXcQ.webp
```

The following `youtube-dl` options are directly responsible for creating the output as described. See [remarks on `youtube-dl`](#remarks-on-youtube-dl) for more details.

```txt
--output '%(id)s.%(ext)s'
--write-info-json
--write-thumbnail
```

Now comes the task of storing the metadata for all videos in a database. First, create a MySQL database for the sole use of this project. You may use any name you like.

```sql
CREATE DATABASE pocket_youtube;
```

Next, create a symbolic link named `videos` to your video library. Please note that for security reasons, Apache disables following symbolic links by default, which means that [http://localhost/youtube/videos/](http://localhost/youtube/videos/) should **not** be accessible.

```sh
ln --symbolic /path/to/video/library videos
```

Create a file named `mysql.php` which contains your MySQL login credentials and database name.

```php
<?php
$hostname = "localhost";
$username = ...;
$password = ...;
$database = "pocket_youtube";
```

Finally, navigate to [http://localhost/youtube/init_db.php](http://localhost/youtube/init_db.php) to initialize the database. Upon returning to the homepage, you should find it populated with your videos.

## Remarks on `youtube-dl`

*Work in progress.*
