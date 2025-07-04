<?php
// Display images stored as base64 in database
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinefoodphp";

$db = mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    $query = "SELECT user_image, image_mime_type FROM users WHERE u_id = ? AND user_image IS NOT NULL";
    $stmt = mysqli_prepare($db, $query);
    
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            $mime_type = $row['image_mime_type'] ? $row['image_mime_type'] : 'image/jpeg';
            $image_data = base64_decode($row['user_image']);
            
            header("Content-Type: " . $mime_type);
            header("Content-Length: " . strlen($image_data));
            echo $image_data;
        } else {
            // Default image
            header("Content-Type: image/png");
            echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($db);
?>