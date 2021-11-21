#!/bin/php
<?php
require_once __DIR__ . "/include/mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);

// recreate all tables
$mysqli->query("DROP TABLE IF EXISTS channel, video");
$mysqli->query(
    "CREATE TABLE channel(
        channel_id  char(24) PRIMARY KEY,
        uploader    varchar(255)
    ) CHARACTER SET ascii, COLLATE ascii_bin"
);
$mysqli->query(
    "CREATE TABLE video(
        video_id    char(11) PRIMARY KEY,
        title       varchar(100) CHARACTER SET utf8mb4,
        description text CHARACTER SET utf8mb4,
        upload_date date,
        channel_id  char(24),
        duration    int,
        view_count  int,
        thumbnail   varchar(255),
        ext         char(4),
        FOREIGN KEY (channel_id) REFERENCES channel(channel_id)
    ) CHARACTER SET ascii, COLLATE ascii_bin"
);

// prepared INSERT statements
$channel_insert = $mysqli->prepare(
    "INSERT INTO channel(
        channel_id, uploader
    ) VALUES(
        ?, ?
    )"
);
$video_insert = $mysqli->prepare(
    "INSERT INTO video(
        video_id,
        title,
        description,
        upload_date,
        channel_id,
        duration,
        view_count,
        thumbnail,
        ext
    ) VALUES(
        ?, ?, ?, ?, ?, ?, ?, ?, ?
    )"
);

foreach (scandir("www/videos") as $a) {
    if (
        is_dir("www/videos/$a") && !is_link("www/videos/$a")
        && preg_match("/^[a-zA-Z0-9_\-]{24}$/", $a)
    ) {
        $channel_dir = "www/videos/$a";

        foreach (scandir($channel_dir) as $b) {
            $filename = "$channel_dir/$b";

            if (is_file($filename) && preg_match("/^.*\.info\.json$/", $b)) {
                $json = file_get_contents($filename);
                $video = json_decode($json);

                $channel_insert->bind_param(
                    "ss",
                    $video->channel_id,
                    $video->uploader
                );
                $video_insert->bind_param(
                    "sssssssss",
                    $video->id,
                    $video->title,
                    $video->description,
                    $video->upload_date,
                    $video->channel_id,
                    $video->duration,
                    $video->view_count,
                    $video->thumbnail,
                    $video->ext
                );

                /**
                 * Must insert into to channel before video to meet foreign key
                 * constraint on video.channel_id
                 */
                $channel_insert->execute();
                $video_insert->execute();

                echo $video->id, PHP_EOL;
            }
        }
    }
}
