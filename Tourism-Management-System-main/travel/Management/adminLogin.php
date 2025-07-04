<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Courgette" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" 
          crossorigin="anonymous">
    <script src="js/bootstrap.min.js"></script>
    <style>
        body {
            background-image: url('home1.jpg');
            background-repeat: no-repeat;
            background-size: cover;
        }

        #div_login {
            border: 1px solid gray;
            border-radius: 3px;
            width: 500px;
            height: 390px;
            box-shadow: 0px 2px 2px 0px gray;
            margin: 0 auto;
            background-color: black;
            opacity: 0.7;
        }

        #div_login h1 {
            margin-top: 0px;
            font-weight: normal;
            padding: 10px;
            background-color: cornflowerblue;
            color: white;
            font-family: sans-serif;
        }

        #div_login div {
            clear: both;
            margin-top: 10px;
            padding: 5px;
        }

        #div_login .textbox {
            width: 96%;
            padding: 7px;
        }

        .text-white {
            color: white;
        }
    </style>

    <title>Login | Admin Panel</title>
</head>

<body>
<center>
    <h1 class="text-center" style="font-size: 60px; font-family: Courgette; color: black;">Admin Panel</h1>
    <br><br><br><br>
    <div class="container">
        <form method="post" action="">
            <div class="align-center" id="div_login">
                <h1 class="text-center">Login</h1>
                <div>
                    <h3 class="text-white">Username:</h3>
                    <input type="text" class="textbox" name="username" placeholder="Enter Username" required />
                </div>
                <div>
                    <h3 class="text-white">Password:</h3>
                    <input type="password" class="textbox" name="password" placeholder="Enter Password" required />
                </div>
                <br>
                <div>
                    <h3><input class="btn btn-success" type="submit" value="Login" name="but_submit" /></h3>
                </div>
            </div>
        </form>
    </div>
</center>

<?php
session_start();

if (isset($_POST['but_submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'surya' && $password === 'surya@2007') {
        $_SESSION['username'] = $username;
        header('Location: Home.php');
        exit();
    } else {
        echo "<h4 class='text-center bg-danger' style='font-weight:bold;'>Invalid username and password</h4>";
    }
}
?>
</body>
</html>
