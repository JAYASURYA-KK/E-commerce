<?php
// Start session at the very beginning
session_start();

// Include database connection
include("connection/connect.php"); 
error_reporting(0); 

// Initialize variables
$message = "";
$success = "";

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);  
    $password = $_POST['password'];
    
    if(!empty($username) && !empty($password)) {
        // Check for admin login first
        if($username == 'admin' && $password == 'admin') {
            $_SESSION["admin_id"] = 1;
            $_SESSION["user_id"] = 1;
            $_SESSION["username"] = "admin";
            $_SESSION["is_admin"] = true;
            $success = "Admin login successful! Redirecting to admin panel...";
            header("refresh:1;url=../adminmain.html");
        } else {
            // Regular user login - use prepared statements for security
            $loginquery = "SELECT * FROM users WHERE username=? AND password=?";
            $stmt = mysqli_prepare($db, $loginquery);
            mysqli_stmt_bind_param($stmt, "ss", $username, md5($password));
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_array($result);
            
            if($row) {
                $_SESSION["user_id"] = $row['u_id']; 
                $_SESSION["username"] = $row['username'];
                $_SESSION["f_name"] = $row['f_name'];
                $_SESSION['play_sound'] = true;
                $success = "Login successful! Redirecting to home...";
                header("refresh:2;url=../home.php");
            } else {
                $message = "Invalid Username or Password!"; 
            }
        }
    } else {
        $message = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login || Tourism Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet'
        href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/login.css">

    <style type="text/css">
    /* Your specified header CSS - unchanged */
    #header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 0.11rem 1rem;
    }

    .navbar-brand {
        color: white !important;
        font-weight: bold;
        font-size: 2rem;
        background: linear-gradient(45deg, #fff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .navbar-brand:hover {
        background: linear-gradient(45deg, #fff, #ffcc00, #fff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transform: scale(1.05);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    .nav-fixed {
        position: fixed;
        width: 100%;
        z-index: 10;
    }

    .navbar {
        background-image: url("../images/menu-bg.jpg");
        padding: 0.95rem 1rem;
        border-radius: 0;
        transition: all 0.3s ease;
    }

    .navbar:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .navbar-nav .nav-item {
        padding-left: 10px;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-item:hover {
        transform: translateY(-2px);
    }

    .navbar-nav .nav-link {
        position: relative;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .navbar-nav .nav-link:hover {
        color: #ffcc00 !important;
        text-shadow: 0 0 8px rgba(255, 204, 0, 0.6);
    }

    .navbar-nav .nav-link::before {
        content: "";
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ffcc00, transparent);
        transition: left 0.5s ease;
    }

    .navbar-nav .nav-link:hover::before {
        left: 100%;
    }

    .navbar-dark .navbar-toggler {
        background-image: none;
        border-color: transparent;
        transition: all 0.3s ease;
    }

    .navbar-dark .navbar-toggler:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .navbar-dark .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 204, 0, 0.25);
    }

    /* Enhanced body and form styles */
    body {
        margin-top: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Roboto', sans-serif;
        animation: backgroundShift 10s ease-in-out infinite alternate;
    }

    @keyframes backgroundShift {
        0% {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        100% {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    }

    .container {
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-module {
        margin-top: 50px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        animation: slideInScale 0.6s ease-out;
        transition: all 0.3s ease;
    }

    .form-module:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    @keyframes slideInScale {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .form {
        padding: 40px;
    }

    .form h2 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
        font-weight: 700;
        font-size: 28px;
        animation: textGlow 2s ease-in-out infinite alternate;
    }

    @keyframes textGlow {
        from {
            text-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        to {
            text-shadow: 0 0 15px rgba(102, 126, 234, 0.6);
        }
    }

    .form input[type="text"],
    .form input[type="password"] {
        width: 100%;
        padding: 15px;
        margin: 10px 0;
        border: 2px solid #e1e5e9;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        position: relative;
    }

    .form input[type="text"]:focus,
    .form input[type="password"]:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: rgba(255, 255, 255, 1);
        outline: none;
        transform: translateY(-2px);
    }

    .form input::placeholder {
        color: #aaa;
        font-style: italic;
        transition: all 0.3s ease;
    }

    .form input:focus::placeholder {
        opacity: 0.7;
        transform: translateX(5px);
    }

    #buttn {
        color: #fff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 15px 20px;
        border-radius: 10px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-top: 20px;
    }

    #buttn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    #buttn:hover::before {
        left: 100%;
    }

    #buttn:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    #buttn:active {
        transform: translateY(0);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
        margin: 15px 0;
        font-weight: 500;
        animation: alertSlide 0.5s ease-out;
    }

    @keyframes alertSlide {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .alert-danger {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
    }

    .alert-success {
        background: linear-gradient(135deg, #51cf66, #40c057);
        color: white;
        box-shadow: 0 5px 15px rgba(81, 207, 102, 0.3);
    }

    .cta {
        text-align: center;
        padding: 20px;
        background: rgba(248, 249, 250, 0.8);
        margin-top: 20px;
        border-radius: 0 0 20px 20px;
        animation: fadeIn 1s ease-out 0.5s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .cta a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
    }

    .cta a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #667eea;
        transition: width 0.3s ease;
    }

    .cta a:hover::after {
        width: 100%;
    }

    .cta a:hover {
        color: #764ba2;
        transform: translateY(-1px);
    }

    /* Logo animation */
    .logo {
        font-size: 2rem;
        font-weight: bold;
        background: linear-gradient(45deg, #fff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* Responsive animations */
    @media (max-width: 768px) {
        .form-module {
            margin: 20px;
            border-radius: 15px;
        }

        .form {
            padding: 25px;
        }

        .form h2 {
            font-size: 24px;
        }
    }

    /* Loading animation for form submission */
    .form.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .form.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 30px;
        height: 30px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    /* Floating particles background effect */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        animation: particleFloat 20s ease-in-out infinite;
        pointer-events: none;
        z-index: -1;
    }

    @keyframes particleFloat {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        33% {
            transform: translateY(-20px) rotate(120deg);
        }

        66% {
            transform: translateY(10px) rotate(240deg);
        }
    }
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header id="header" class="header-scroll top-header headrom">
        <script src="../google-translate-widget.js"></script>
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse"
                    data-target="#mainNavbarCollapse">&#9776;</button>
                <div class="header-container">
                    <div class="logo">JS Weby</div>
                    <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="../home.php">Home <span
                                        class="sr-only">(current)</span></a>
                            </li>

                            <?php
                            if(empty($_SESSION["user_id"])) {
                                
                            } else {
                                echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a></li>';
                                echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="pen-title"></div>

        <div class="module form-module">
            <div class="toggle"></div>
            <div class="form">
                <h2>Login to your account</h2>

                <?php if($message): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <form action="" method="post" id="loginForm">
                    <input type="text" placeholder="Username" name="username" required />
                    <input type="password" placeholder="Password" name="password" required />
                    <input type="submit" id="buttn" name="submit" value="Login" />
                </form>
            </div>

            <div class="cta">
                Not registered? <a href="registration.php">Create an account</a>
            </div>

            <!-- Admin Login Instructions -->
            <div class="cta" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                <small style="color: #666;"><i class="fa fa-info-circle"></i> Admin Login: Use username "admin" and
                    password "admin"</small>
            </div>
        </div>

        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
        <script src="js/bootstrap.min.js"></script>

        <script>
        // Add loading animation on form submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.querySelector('.form').classList.add('loading');
        });

        // Add input focus animations
        document.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        </script>

        <div class="container-fluid pt-3">
            <p></p>
        </div>
    </div>
</body>

</html>