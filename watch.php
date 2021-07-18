<?php
require_once "mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);

$stmt = $mysqli->prepare(
    "SELECT id, title, description, ext
    FROM video
    WHERE id=?"
);
$stmt->bind_param("s", $_GET["v"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false || !($row = $result->fetch_assoc())) {
    http_response_code(404);
    echo "Video not found";
    die();
}

$video_url = "/videos/{$row['id']}.{$row['ext']}";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $row["title"]; ?></title>
</head>

<body>
    <h1><?php echo $row["title"]; ?></h1>
    <video controls src="<?php echo $video_url; ?>">
        <em>Your browser does not support embedded videos</em>
    </video>
    <pre><?php echo $row["description"]; ?></pre>
</body>

</html>
