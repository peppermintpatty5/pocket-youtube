<?php
require_once "mysql.php";

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
    <ol>
        <?php
        $stmt = $mysqli->prepare(
            "SELECT video_id, title, upload_date, thumbnail
            FROM video
            WHERE channel_id=?
            ORDER BY upload_date DESC"
        );
        $stmt->bind_param("s", $channel_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $video_id = $row["video_id"];
            $title = $row["title"];
            $thumbnail = $row["thumbnail"];

            /**
             * prefer the locally hosted thumbnail, obtain by extracting the
             * extension (jpg, webp, etc.) from YouTube's URL using regex
             */
            if (preg_match("/\.([[:alnum:]]+)(\?.*)?$/", $thumbnail, $matches)) {
                $thumb_ext = $matches[1];
                $thumbnail = "/videos/{$video_id}.{$thumb_ext}";
            }

            $watch_url = "watch.php?id={$video_id}";
        ?>
            <li>
                <img width="240" height="150" src="<?php echo $thumbnail; ?>">
                <a href="<?php echo $watch_url; ?>"><?php echo $title; ?></a>
            </li>
        <?php } ?>
    </ol>
</body>

</html>
