<?php
session_start(); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinefoodphp";

$db = mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST['submit'])) {
    // Validate required fields
    if(empty($_POST['name']) || 
       empty($_POST['firstname']) || 
       empty($_POST['lastname']) || 
       empty($_POST['email']) ||  
       empty($_POST['phone']) ||
       empty($_POST['username']) ||
       empty($_POST['password']) ||
       empty($_POST['repeatPassword']) ||
       empty($_POST['addressLine1']) ||
       empty($_POST['city']) ||
       empty($_POST['state'])) {
        $message = "All required fields must be filled!";
    } else {
        // Sanitize inputs
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
        $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $phone = mysqli_real_escape_string($db, $_POST['phone']);
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $password = $_POST['password'];
        $repeatPassword = $_POST['repeatPassword'];
        $addressLine1 = mysqli_real_escape_string($db, $_POST['addressLine1']);
        $addressLine2 = mysqli_real_escape_string($db, $_POST['addressLine2']);
        $city = mysqli_real_escape_string($db, $_POST['city']);
        $state = mysqli_real_escape_string($db, $_POST['state']);
        $address = $addressLine1 . ', ' . $addressLine2 . ', ' . $city . ', ' . $state;
        
        // Handle image upload - Store as Base64 encoded string
        $user_image_base64 = null;
        $image_mime_type = null;
        
        if(isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
            $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
            $file_mime_type = $_FILES['user_image']['type'];
            $file_size = $_FILES['user_image']['size'];
            
            // Validate file type
            if(in_array($file_mime_type, $allowed_types)) {
                // Validate file size (5MB limit)
                if($file_size <= 5 * 1024 * 1024) {
                    // Read the image file and convert to base64
                    $image_tmp_name = $_FILES['user_image']['tmp_name'];
                    
                    if(file_exists($image_tmp_name)) {
                        $image_data = file_get_contents($image_tmp_name);
                        $user_image_base64 = base64_encode($image_data);
                        $image_mime_type = $file_mime_type;
                        
                        // Additional validation
                        $image_info = getimagesize($image_tmp_name);
                        if($image_info === false) {
                            echo "<script>alert('Invalid image file!');</script>";
                            $user_image_base64 = null;
                            $image_mime_type = null;
                        }
                    }
                } else {
                    echo "<script>alert('Image file size must be less than 5MB!');</script>";
                }
            } else {
                echo "<script>alert('Invalid image format! Please use JPG, JPEG, PNG, or GIF.');</script>";
            }
        }
        
        // Check for existing username and email
        $check_username = mysqli_query($db, "SELECT username FROM users WHERE username = '$username'");
        $check_email = mysqli_query($db, "SELECT email FROM users WHERE email = '$email'");
        
        // Validation checks
        if($password != $repeatPassword) {  
            echo "<script>alert('Passwords do not match!');</script>"; 
        }
        elseif(strlen($password) < 6) {
            echo "<script>alert('Password must be at least 6 characters long!');</script>"; 
        }
        elseif(strlen($phone) < 10) {
            echo "<script>alert('Invalid phone number! Must be at least 10 digits.');</script>"; 
        }
        elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email address! Please enter a valid email.');</script>"; 
        }
        elseif(mysqli_num_rows($check_username) > 0) {
            echo "<script>alert('Username already exists! Please choose a different username.');</script>"; 
        }
        elseif(mysqli_num_rows($check_email) > 0) {
            echo "<script>alert('Email already exists! Please use a different email.');</script>"; 
        }
        else {
            // Insert new user with base64 encoded image
            $hashed_password = md5($password);
            
            // Simple INSERT query with base64 encoded image
            $sql = "INSERT INTO users (username, f_name, l_name, full_name, email, phone, password, user_image, image_mime_type, address, address_line1, address_line2, city, state) 
                    VALUES ('$username', '$firstname', '$lastname', '$name', '$email', '$phone', '$hashed_password', " . 
                    ($user_image_base64 ? "'$user_image_base64'" : "NULL") . ", " . 
                    ($image_mime_type ? "'$image_mime_type'" : "NULL") . ", '$address', '$addressLine1', '$addressLine2', '$city', '$state')";
            
            if(mysqli_query($db, $sql)) {
                echo "<script>alert('Registration successful! You can now login.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Registration failed! Error: " . mysqli_error($db) . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration || Tourism Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet'
        href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <style type="text/css">
    /* Header CSS - Same as login */
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
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
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

    .form-group {
        margin-bottom: 25px;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-group:nth-child(1) {
        animation-delay: 0.1s;
    }

    .form-group:nth-child(2) {
        animation-delay: 0.2s;
    }

    .form-group:nth-child(3) {
        animation-delay: 0.3s;
    }

    .form-group:nth-child(4) {
        animation-delay: 0.4s;
    }

    .form-group:nth-child(5) {
        animation-delay: 0.5s;
    }

    .form-group:nth-child(6) {
        animation-delay: 0.6s;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form input[type="text"],
    .form input[type="email"],
    .form input[type="password"],
    .form input[type="file"] {
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
    .form input[type="email"]:focus,
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

    .row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .col-sm-6 {
        flex: 1;
        min-width: 250px;
    }

    .col-sm-12 {
        width: 100%;
    }

    #buttn {
        color: #fff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 15px 35px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-top: 20px;
        width: auto;
        display: block;
        margin-left: auto;
        margin-right: auto;
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

    /* Image upload styling */
    .image-upload-label {
        display: block;
        padding: 30px 20px;
        border: 2px dashed #667eea;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        background: rgba(102, 126, 234, 0.05);
        transition: all 0.3s ease;
        margin: 10px 0;
        position: relative;
        overflow: hidden;
    }

    .image-upload-label::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .image-upload-label:hover::before {
        left: 100%;
    }

    .image-upload-label:hover {
        background: rgba(102, 126, 234, 0.1);
        border-color: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }

    .image-preview {
        max-width: 150px;
        max-height: 150px;
        margin: 15px auto;
        display: none;
        border: 2px solid #667eea;
        border-radius: 10px;
        object-fit: cover;
        animation: imageZoom 0.3s ease-out;
    }

    @keyframes imageZoom {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive Design */
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

        .row {
            flex-direction: column;
        }

        .col-sm-6 {
            min-width: 100%;
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

    /* Header container styling */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        margin: 0 10px;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .navbar-toggler {
        display: none;
        background: none;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
    }

    @media (max-width: 768px) {
        .navbar-nav {
            display: none;
        }

        .navbar-toggler {
            display: block;
        }
    }
    </style>
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
                            <li class="nav-item">
                                <a class="nav-link active" href="login.php">Login</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="form-module">
            <div class="form">
                <h2>Create Your Account</h2>

                <!-- PHP Alert Messages would go here -->
                <!-- <?php if(isset($message)) { echo "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> $message</div>"; } ?> -->

                <form action="" method="post" enctype="multipart/form-data" id="registrationForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" placeholder="Enter your full name here" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" placeholder="Enter a username here" required>
                    </div>

                    <div class="form-group">
                        <label for="user_image">Profile Image (Optional)</label>
                        <label for="user_image" class="image-upload-label">
                            <i class="fa fa-camera"
                                style="font-size: 24px; color: #667eea; margin-bottom: 10px;"></i><br>
                            <span>Click to upload profile image</span><br>
                            <small>Supported formats: JPG, JPEG, PNG, GIF (Max 5MB)</small>
                        </label>
                        <input type="file" id="user_image" name="user_image" accept="image/*" style="display: none;">
                        <img id="imagePreview" class="image-preview" alt="Image Preview">
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="firstname">First Name</label>
                            <input type="text" name="firstname" placeholder="Enter your first name" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="lastname">Last Name</label>
                            <input type="text" name="lastname" placeholder="Enter your last name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email here" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="phone">Phone Number</label>
                            <input type="text" name="phone" placeholder="Enter your phone number here" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="password">Password</label>
                            <input type="password" name="password" placeholder="Enter a password here" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="repeatPassword">Repeat Password</label>
                            <input type="password" name="repeatPassword" placeholder="Enter the same password again"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="addressLine1">Address Line 1</label>
                            <input type="text" name="addressLine1" placeholder="House No./Flat No./Apartment No."
                                required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="addressLine2">Address Line 2</label>
                            <input type="text" name="addressLine2" placeholder="Lane, Locality">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="city">City</label>
                            <input type="text" name="city" placeholder="Enter your city" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="state">State</label>
                            <input type="text" name="state" placeholder="Enter your state" required>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 40px;">
                        <input type="submit" value="Create Account" name="submit" id="buttn">
                    </div>
                </form>
            </div>

            <div class="cta">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
    // Add loading animation on form submit
    document.getElementById('registrationForm').addEventListener('submit', function() {
        document.querySelector('.form').classList.add('loading');
    });

    // Add input focus animations
    document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';

            // Validation styling
            if (this.value.trim() === '' && this.hasAttribute('required')) {
                this.style.borderColor = '#ef4444';
            } else {
                this.style.borderColor = '#10b981';
            }
        });
    });

    // Image preview functionality
    document.getElementById('user_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const label = document.querySelector('.image-upload-label span');

        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                label.innerHTML = 'Image selected: ' + file.name;
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            label.innerHTML = 'Click to upload profile image';
        }
    });

    // Password confirmation validation
    const password = document.querySelector('input[name="password"]');
    const repeatPassword = document.querySelector('input[name="repeatPassword"]');

    repeatPassword.addEventListener('input', function() {
        if (password.value !== this.value) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '#10b981';
        }
    });

    // Staggered animation for form groups
    document.querySelectorAll('.form-group').forEach((group, index) => {
        group.style.animationDelay = (index * 0.1) + 's';
    });
    </script>
</body>

</html>