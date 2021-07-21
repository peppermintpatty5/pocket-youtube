<?php
require_once "mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);

$stmt = $mysqli->prepare(
    "SELECT video_id, title, description, ext
    FROM video
    WHERE video_id=?"
);
$stmt->bind_param("s", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $video_id = $row["video_id"];
    $title = $row["title"];
    $description = $row["description"];
    $ext = $row["ext"];
} else {
    http_response_code(404);
    echo "Video not found";
    die();
}

$video_url = "/videos/{$video_id}.{$ext}";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
</head>

<body>
    <h1><?php echo $title; ?></h1>
    <video controls src="<?php echo $video_url; ?>">
        <em>Your browser does not support embedded videos</em>
    </video>
    <pre><?php echo $description; ?></pre>
</body>

</html>
