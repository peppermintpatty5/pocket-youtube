<?php
require_once "mysql.php";

const VIDEO_DIR = "videos";

$mysqli = new mysqli($hostname, $username, $password, $database);

$mysqli->query("DROP TABLE IF EXISTS video");
$mysqli->query(
    "CREATE TABLE video(
        id VARCHAR(15) PRIMARY KEY,
        title VARCHAR(255),
        description TEXT,
        ext VARCHAR(7)
    ) COLLATE=utf8mb4_0900_bin"
);
$stmt = $mysqli->prepare(
    "INSERT INTO video(
        id, title, description, ext
    ) VALUES(
        ?, ?, ?, ?
    )"
);

$files = scandir(VIDEO_DIR);
foreach ($files as $file) {
    if (preg_match("/^.*\.info\.json$/", $file)) {
        $json = file_get_contents(VIDEO_DIR . "/$file");
        $data = json_decode($json, true);

        $stmt->bind_param(
            "ssss",
            $data["id"],
            $data["title"],
            $data["description"],
            $data["ext"]
        );
        $stmt->execute();
    }
}
