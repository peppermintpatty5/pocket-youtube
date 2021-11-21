# pocket-youtube

A simple website for hosting your archived YouTube videos.

## Prerequisites

You will need the following tools, commonly referred to as LAMP.

- Linux
- Apache
- MySQL
- PHP :persevere:

> "Your father's LAMP-saber. This is the weapon of a web engineer. Not as clumsy or random as Node.js. An elegant stack for a more civilized age."
>
> \- *Obi-Wan Kenobi*

## Installation

1. Create a database which will be used to store video metadata. You may choose any name for this.

    ```sql
    CREATE DATABASE `pocket_youtube`;
    ```

2. Create a file `config.ini` which contains your MySQL credentials.

    ```ini
    [mysql]
    hostname = "localhost"
    username = "username"
    password = "password"
    database = "pocket_youtube"
    ```

3. Create a symbolic link to your video library.

    ```sh
    ln --symbolic /path/to/video/library www/videos
    ```

4. Run the script [`init_db.php`](init_db.php) to initialize the database.

    ```sh
    php init_db.php
    ```

5. To quickly check if things are working, you can start PHP's built-in web server on <http://localhost:8000>.

    ```sh
    php -S localhost:8000 -t www/
    ```

## Configuring Apache

Configuring the Apache webserver can be a headache. The shell script [`install.sh`](install.sh) is meant for Debian-based systems. On other systems, Apache is called `httpd` and the configuration files are in different locations.

Chances are you cloned this repository into your home directory. If you use `install.sh`, you will need to give global read permission to your home directory. Some will rightfully see this as a security concern. I suggest you avoid using `install.sh` and do your own research.

## Videos Directory

This project requires `videos/` to be in a specific format. All files must be named by their YouTube ID, which guarantees unique and simple filenames. Metadata and thumbnails for each video are also needed.

Furthermore, `videos/` must be partitioned by channel. These subdirectories must be named using the 24-character YouTube channel ID. The user is encouraged, but not required, to create symbolic links to these directories for easier reference.

```txt
videos/
├── TheAngryGrandpaShow -> UCPFVhmjjSkFhfstm2LghZIg/
└── UCPFVhmjjSkFhfstm2LghZIg
    ├── zVQ61CpWBRk.info.json
    ├── zVQ61CpWBRk.mp4
    └── zVQ61CpWBRk.webp
```

The following `youtube-dl` options are directly responsible for creating the output as described. See [remarks on `youtube-dl`](#remarks-on-youtube-dl) for more details.

```txt
--output '%(id)s.%(ext)s'
--write-info-json
--write-thumbnail
```

## Remarks on `youtube-dl`

*Work in progress.*
