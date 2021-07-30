<?php
require_once "mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);

$stmt = $mysqli->prepare(
    "SELECT
        video.video_id AS id,
        video.title AS title,
        video.description AS description,
        video.ext AS ext
    FROM video
    WHERE
        video.video_id=?"
);
$stmt->bind_param("s", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result();

if (!($result && $video = $result->fetch_object())) {
    http_response_code(404);
    echo "Video not found";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $video->title; ?></title>
</head>

<body>
    <h1><?php echo $video->title; ?></h1>
    <video controls src="/videos/<?php echo "{$video->id}.{$video->ext}"; ?>">
        <em>Your browser does not support embedded videos</em>
    </video>
    <pre><?php echo $video->description; ?></pre>
</body>

</html>
