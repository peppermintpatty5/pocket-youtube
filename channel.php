<?php
require_once "mysql.php";

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
    "SELECT channel_id, uploader
    FROM channel
    WHERE channel_id=?"
);
$stmt->bind_param("s", $_GET["id"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $channel = $result->fetch_object()) {
    $channel_id = $channel->channel_id;
    $uploader = $channel->uploader;
} else {
    http_response_code(404);
    echo "Channel not found";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $uploader; ?></title>
</head>

<body>
    <h1><?php echo $uploader; ?></h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Thumbnail</th>
                <th>Duration</th>
                <th>Title</th>
                <th>View count</th>
                <th>Upload date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $mysqli->prepare(
                "SELECT video_id, title, upload_date, duration, view_count, thumbnail
                FROM video
                WHERE channel_id=?
                ORDER BY upload_date DESC"
            );
            $stmt->bind_param("s", $channel_id);
            $stmt->execute();
            $result = $stmt->get_result();

            for ($i = 1; $video = $result->fetch_object(); $i++) {
                /**
                 * Prefer the locally hosted thumbnail, obtain by extracting the
                 * extension (jpg, webp, etc.) from YouTube's URL using regex
                 */
                $thumbnail = $video->thumbnail;
                if (preg_match("/\.([[:alnum:]]+)(\?.*)?$/", $thumbnail, $matches)) {
                    $thumb_ext = $matches[1];
                    $thumbnail = "/videos/{$video->video_id}.{$thumb_ext}";
                }
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                        <img width="240" height="150" src="<?php echo $thumbnail; ?>">
                    </td>
                    <td><?php echo format_duration($video->duration); ?></td>
                    <td>
                        <a href="watch.php?id=<?php echo $video->video_id; ?>">
                            <?php echo $video->title; ?>
                        </a>
                    </td>
                    <td><?php echo number_format($video->view_count); ?></td>
                    <td><?php echo date_format(
                            date_create($video->upload_date),
                            "M j, Y"
                        ); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
