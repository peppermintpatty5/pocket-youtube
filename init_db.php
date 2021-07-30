<?php
require_once "mysql.php";

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

$files = scandir("videos");
foreach ($files as $file) {
    if (preg_match("/^.*\.info\.json$/", $file)) {
        $json = file_get_contents("videos/{$file}");
        $data = json_decode($json, true);

        $channel_insert->bind_param(
            "ss",
            $data["channel_id"],
            $data["uploader"]
        );
        $video_insert->bind_param(
            "sssssssss",
            $data["id"], // video_id
            $data["title"],
            $data["description"],
            $data["upload_date"],
            $data["channel_id"],
            $data["duration"],
            $data["view_count"],
            $data["thumbnail"],
            $data["ext"]
        );

        /**
         * Must insert into to channel before video to meet foreign key
         * constraint on video.channel_id
         */
        $channel_insert->execute();
        $video_insert->execute();
    }
}
