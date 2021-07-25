<?php
require_once "mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);

// recreate all tables
$mysqli->query("DROP TABLE IF EXISTS channel, video");
$mysqli->query(
    "CREATE TABLE channel(
        channel_id varchar(24) PRIMARY KEY,
        uploader varchar(255)
    ) COLLATE=utf8mb4_0900_bin"
);
$mysqli->query(
    "CREATE TABLE video(
        video_id varchar(15) PRIMARY KEY,
        title varchar(255),
        description text,
        upload_date date,
        channel_id varchar(24),
        duration int,
        view_count int,
        thumbnail varchar(255),
        ext varchar(7),
        FOREIGN KEY (channel_id) REFERENCES channel(channel_id)
    ) COLLATE=utf8mb4_0900_bin"
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
