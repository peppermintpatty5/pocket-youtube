<?php
require_once "mysql.php";

const VIDEO_DIR = "videos";

$mysqli = new mysqli($hostname, $username, $password, $database);

$mysqli->query("DROP TABLE IF EXISTS video");
$mysqli->query("CREATE TABLE video(
    id VARCHAR(15) PRIMARY KEY,
    title VARCHAR(255))");
$stmt = $mysqli->prepare("INSERT INTO video(id, title) VALUES (?, ?)");

$files = scandir(VIDEO_DIR);
foreach ($files as $file) {
    if (preg_match("/^.*\.info\.json$/", $file)) {
        $json = file_get_contents(VIDEO_DIR . "/$file");
        $data = json_decode($json, true);

        $stmt->bind_param("ss", $data["id"], $data["title"]);
        $stmt->execute();
    }
}
