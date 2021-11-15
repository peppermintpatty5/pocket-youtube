<?php
require_once __DIR__ . "/../include/mysql.php";

/**
 * Given a duration in seconds as a non-negative integer, outputs a string in
 * the format `mm:ss` or `h:mm:ss`.
 */
function format_duration(int $duration): string
{
    $s = $duration % 60;
    $m = intdiv($duration, 60) % 60;
    $h = intdiv($duration, 3600);

    if ($h > 0)
        return sprintf("%d:%02d:%02d", $h, $m, $s);
    else
        return sprintf("%d:%02d", $m, $s);
}

$mysqli = new mysqli($hostname, $username, $password, $database);

$stmt = $mysqli->prepare(
    "SELECT
        channel.channel_id AS id,
        channel.uploader AS uploader
    FROM channel
    WHERE
        channel.channel_id=?"
);
$stmt->bind_param("s", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result();

if (!($result && $channel = $result->fetch_object())) {
    http_response_code(404);
    echo "Channel not found";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($channel->uploader); ?></title>
    <link rel="stylesheet" href="styles/channel.css">
</head>

<body>
    <h1><?php echo htmlspecialchars($channel->uploader); ?></h1>
    <main>
        <?php
        $stmt = $mysqli->prepare(
            "SELECT
                video.video_id AS id,
                video.title AS title,
                video.upload_date AS upload_date,
                video.channel_id AS channel_id,
                video.duration AS duration,
                video.view_count AS view_count,
                video.thumbnail AS thumbnail
            FROM video
            WHERE
                video.channel_id=?
            ORDER BY video.upload_date"
        );
        $stmt->bind_param("s", $channel->id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($video = $result->fetch_object()) {
            /**
             * Prefer the locally hosted thumbnail, obtain by extracting the
             * extension (jpg, webp, etc.) from YouTube's URL using regex
             */
            $thumbnail = $video->thumbnail;
            if (preg_match("/\.([[:alnum:]]+)(\?.*)?$/", $thumbnail, $matches)) {
                $thumb_ext = $matches[1];
                $thumbnail = "/videos/$video->channel_id/$video->id.$thumb_ext";
            } ?>
            <figure class="video-block">
                <div class="video-thumbnail">
                    <img width="240" height="150" alt="" src="<?php echo $thumbnail; ?>">
                    <span class="video-duration">
                        <?php echo format_duration($video->duration); ?></span>
                </div>
                <figcaption>
                    <p class="video-title">
                        <a href="watch.php?id=<?php echo $video->id; ?>">
                            <?php echo htmlspecialchars($video->title); ?></a>
                    </p>
                    <span class="view-count">
                        <?php echo number_format($video->view_count); ?> views &bull;</span>
                    <span class="upload-date">
                        <?php echo date_format(
                            date_create($video->upload_date),
                            "M j, Y"
                        ); ?></span>
                </figcaption>
            </figure>
        <?php } ?>
    </main>
</body>

</html>
