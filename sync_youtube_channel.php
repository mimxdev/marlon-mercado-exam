<?php
session_start();
ob_start();

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

if (isset($_POST['youtube-channel'])){

    $channelLink = $_POST['youtube-channel'];
    $username = explode('@', $channelLink, 2)[1];

    $youtubeApiKey = 'AIzaSyDK0j2-kbGPLJhLQOKXEuuif-FbKj2_C8w';
    $youtubeChannelUrl = 'https://youtube.googleapis.com/youtube/v3/channels?part=snippet&contentDetails%2Cstatistics&forUsername='. $username . '&key=' . $youtubeApiKey;  
    $youtubeChannelDetails = file_get_contents($youtubeChannelUrl);

    if ($youtubeChannelDetails){

        $youtubeChannelDetails = json_decode($youtubeChannelDetails, true);
        $channelId = $youtubeChannelDetails['items'][0]['id'];
        
        if ($channelId == ''){

            $_SESSION['error'] = 'PLEASE TRY ANOTHER CHANNEL URL.';
            header('location: index.php');
            exit;

        } else {
    
            $youtubeVideosUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=date&key=' . $youtubeApiKey . '&channelId='. $channelId .'&maxResults=50';
            $youtubeVideosUrl2 = file_get_contents($youtubeVideosUrl);
            $youtubeVideosUrl2 = json_decode($youtubeVideosUrl2, true);

            //check if tables are not empty
            checkTables ($connection);
        
           //save first 50 videos and channel info
            saveChannelInfo($youtubeChannelUrl, $connection);
            saveYoutubeVideos($youtubeVideosUrl, $connection);

            $youtubeVideosUrl .= '&pageToken=' . $youtubeVideosUrl2['nextPageToken'];

            //save another 50 videos
            saveYoutubeVideos($youtubeVideosUrl, $connection);

            header('location: show_youtube_channel.html');   
        }
    }  
}

function saveChannelInfo($channelUrl, $connection){

    $youtubeChannelInfo = file_get_contents($channelUrl);

    if($youtubeChannelInfo){

        $youtubeChannelInfo = json_decode($youtubeChannelInfo, true);

        //save channel details
        $channelProfile = $connection->real_escape_string ($youtubeChannelInfo['items']['0']['snippet']['thumbnails']['high']['url']);
        $channelName = $connection->real_escape_string ($youtubeChannelInfo['items']['0']['snippet']['title']);
        $channelDesc = $connection->real_escape_string ($youtubeChannelInfo['items']['0']['snippet']['description']);  
        
        $queryAddChannel = "INSERT 
                            INTO 
                            youtube_channels (
                            profile_picture,
                            name,
                            description   
                            ) 
                            VALUES (
                            '$channelProfile',
                            '$channelName',
                            '$channelDesc'
                            )";
        $sqlAddChannel = mysqli_query($connection, $queryAddChannel);

        if($sqlAddChannel){
            echo '<b>'. $channelName . '</b> Added Successfully. <br>';
        } else {
            echo 'Something went wrong when saving <b>' . $channelName .'</b><br>';
        }

    } else {
         echo 'Something went wrong.';
    }
}

function saveYoutubeVideos($videoUrl, $connection) {

    $youtubeVideos = file_get_contents($videoUrl);

    if($youtubeVideos){

        $youtubeVideos = json_decode($youtubeVideos, true);

        if(!empty($youtubeVideos ['items'])){
            foreach($youtubeVideos['items'] as $videoDetails){
                if($videoDetails['id']['kind']  == 'youtube#video'){

                    //save video details
                    $videoLink = $connection->real_escape_string ('https://www.youtube.com/watch?v=' . $videoDetails['id']['videoId']);
                    $videoTitle = $connection->real_escape_string ($videoDetails['snippet']['title']);
                    $videoDesc = $connection->real_escape_string ($videoDetails['snippet']['description']);
                    $videoThumbnail = $connection->real_escape_string ($videoDetails['snippet']['thumbnails']['default']['url']);

                    $queryAddVideos = "INSERT 
                                       INTO 
                                       youtube_channel_videos (
                                       video_link,
                                       title,
                                       description,
                                       thumbnail   
                                       ) 
                                       VALUES (
                                       '$videoLink',
                                       '$videoTitle',
                                       '$videoDesc',
                                       '$videoThumbnail'
                                       )";
                    $sqlAddVideos = mysqli_query($connection, $queryAddVideos);

                    if($sqlAddVideos){
                        echo '<b>'. $videoTitle . '</b> Added Successfully.<br>';
                    } else {
                        echo 'Something went wrong when saving <b>' . $videoTitle .'</b><br>' . $connection->error . '<br>';
                    }
                }
            }
        }
    } else {
        echo 'Something went wrong.';
    }
}

function checkTables ($connection){
    $sql = "SELECT * FROM youtube_channels";  
    $query = mysqli_query($connection, $sql);

    $sql2 = "SELECT * FROM youtube_channel_videos";  
    $query2 = mysqli_query($connection, $sql2);

    if ($query->num_rows > 0) {
        $sql = "DELETE FROM youtube_channels";  
        $query = mysqli_query($connection, $sql);
    }

     if ($query2->num_rows > 0) {
        $sql = "DELETE FROM youtube_channel_videos";  
        $query = mysqli_query($connection, $sql);
    }
}

ob_end_flush();
?>