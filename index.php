<?php
require_once "mysql.php";

$mysqli = new mysqli($hostname, $username, $password, $database);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pocket YouTube</title>
</head>

<body>
    <h1>Pocket YouTube</h1>
    <ul>
        <?php
        $result = $mysqli->query("SELECT video_id, title FROM video");
        while ($row = $result->fetch_assoc()) {
            $href = "watch.php?v={$row['video_id']}";
            echo "<li><a href='$href'>", $row["title"], "</a></li>";
        }
        ?>
    </ul>
</body>

</html>
