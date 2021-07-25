<?php
require_once "mysql.php";

function duration_to_timestamp($duration)
{
    $seconds = $duration % 60;
    $minutes = intdiv($duration, 60);

    return sprintf("%d:%02d", $minutes, $seconds);
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

if ($result && $row = $result->fetch_assoc()) {
    $channel_id = $row["channel_id"];
    $uploader = $row["uploader"];
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
    <title><?php echo $row["uploader"]; ?></title>
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

            for ($i = 1; $row = $result->fetch_assoc(); $i++) {
                $video_id = $row["video_id"];
                $title = $row["title"];
                $upload_date = $row["upload_date"];
                $duration = $row["duration"];
                $view_count = $row["view_count"];
                $thumbnail = $row["thumbnail"];

                /**
                 * Prefer the locally hosted thumbnail, obtain by extracting the
                 * extension (jpg, webp, etc.) from YouTube's URL using regex
                 */
                if (preg_match("/\.([[:alnum:]]+)(\?.*)?$/", $thumbnail, $matches)) {
                    $thumb_ext = $matches[1];
                    $thumbnail = "/videos/{$video_id}.{$thumb_ext}";
                }

                $watch_url = "watch.php?id={$video_id}";
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                        <img width="240" height="150" src="<?php echo $thumbnail; ?>">
                    </td>
                    <td><?php echo duration_to_timestamp($duration); ?></td>
                    <td>
                        <a href="<?php echo $watch_url; ?>"><?php echo $title; ?></a>
                    </td>
                    <td><?php echo number_format($view_count); ?></td>
                    <td><?php echo $upload_date; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
