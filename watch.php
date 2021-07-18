<?php
$video_url = "/videos/{$_GET['v']}.mp4";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<body>
    <video controls src="<?php echo $video_url; ?>"></video>
</body>

</html>
