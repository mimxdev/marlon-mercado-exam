<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youtube Channel Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    <div id="container" class="container">
        <div class="card card-index p-5">
            <p>ENTER YOUTUBE CHANNEL URL</p>
            <form action="sync_youtube_channel.php" method="post">
                <input type="text" name="youtube-channel" class="form-control" autocomplete="off" placeholder="https://www.youtube.com/@NBA" required>
                <input type="submit" id="getChannelInfo" value="SUBMIT" class="btn btn-sm btn-primary m-2">
        </form>
        <?php
            if(isset($_SESSION['error'])){
                echo '<div class="alert alert-danger" role="alert">' .$_SESSION['error']. '</div>';
                unset($_SESSION['error']);
             }
        ?>
        </div>
    </div>
</body>
</html>