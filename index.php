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
        $result = $mysqli->query("SELECT channel_id, uploader FROM channel");

        while ($row = $result->fetch_assoc()) {
            $channel_url = "channel.php?id={$row['channel_id']}";
        ?>
            <li>
                <a href="<?php echo $channel_url; ?>">
                    <?php echo $row["uploader"] ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</body>

</html>
