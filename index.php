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
        $result = $mysqli->query(
            "SELECT
                channel.channel_id AS id,
                channel.uploader AS uploader,
                COUNT(video.video_id) AS video_count
            FROM channel
                LEFT JOIN video ON channel.channel_id=video.channel_id
            GROUP BY channel.channel_id
            ORDER BY channel.uploader"
        );

        while ($channel = $result->fetch_object()) {
        ?>
            <li>
                <a href="channel.php?id=<?php echo $channel->id; ?>">
                    <?php echo htmlspecialchars($channel->uploader) ?></a>
                <span><?php echo $channel->video_count; ?> videos</span>
            </li>
        <?php } ?>
    </ul>
</body>

</html>
