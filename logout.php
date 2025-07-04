<?php
session_start();

// Destroy all session data
session_destroy();

// Redirect to home page
header("Location:Online-Food-Ordering-System-in-PHP-main/login.php");
exit();
?>