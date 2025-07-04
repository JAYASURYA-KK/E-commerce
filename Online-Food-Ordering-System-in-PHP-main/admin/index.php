<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded login for surya
    if ($username === 'surya' && $password === 'surya@2007') {
        $_SESSION["adm_id"] = 'hardcoded_surya';
        header("Location: dashboard.php");
        exit();
    }

    // Otherwise, check in database
    $loginquery = "SELECT * FROM admin WHERE username='$username' && password='$password'";
    $result = mysqli_query($db, $loginquery);
    $row = mysqli_fetch_array($result);

    if (is_array($row)) {
        $_SESSION["adm_id"] = $row['adm_id'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Username or Password!');</script>";
    }
}


?>

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <link rel="stylesheet" href="css/login.css">


</head>

<body>



    <div class="container">
        <div class="info">
            <h1>Admin Panel </h1>
        </div>
    </div>
    <div class="form">
        <div class="thumbnail"><img src="images/manager.png" /></div>
        <span style="color:red;"><?php echo $message; ?></span>
        <span style="color:green;"><?php echo $success; ?></span>
        <form class="login-form" action="index.php" method="post">
            <input type="text" placeholder="Username" name="username" />
            <input type="password" placeholder="Password" name="password" />
            <input type="submit" name="submit" value="Login" />

        </form>

    </div>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='js/index.js'></script>
</body>

</html>