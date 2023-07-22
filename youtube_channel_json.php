<?php
//connect database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'youtube_db';
    
$connection = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_error()){
    echo "Connection Failed. <br>";
    echo "Message: " .mysqli_connect_error();
}

$arrayVideoLists = array();
$arrayChannelLists = array();

$queryVideoLists = "SELECT ROW_NUMBER() OVER(ORDER BY id) AS row_number, video_link, title, description, thumbnail FROM youtube_channel_videos";
$sqlVideoLists = mysqli_query($connection, $queryVideoLists);

while($videoResults = mysqli_fetch_assoc($sqlVideoLists)) {
    $arrayVideoLists[] = $videoResults;
}

$queryChannelLists = "SELECT profile_picture, name, description FROM youtube_channels";
$sqlChannelLists = mysqli_query($connection, $queryChannelLists);

while($channelResults = mysqli_fetch_assoc($sqlChannelLists)) {
    $arrayChannelLists[] = $channelResults;
}

$dataset = array(
    "videos" => $arrayVideoLists,
    "channels" => $arrayChannelLists
);

echo json_encode($dataset);

?>