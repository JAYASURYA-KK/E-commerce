<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_FOOD', 'onlinefoodphp');
define('DB_TRAVEL', 'projectmeteor');

// Create database connections with error handling
function getDBConnection($database) {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, $database);
    
    if ($connection->connect_error) {
        error_log("Database connection failed: " . $connection->connect_error);
        return false;
    }
    
    $connection->set_charset("utf8");
    return $connection;
}

// Initialize database connections
$db_food = getDBConnection(DB_FOOD);
$db_travel = getDBConnection(DB_TRAVEL);

if (!$db_food) {
    die("Food database connection failed. Please check your configuration.");
}

if (!$db_travel) {
    die("Travel database connection failed. Please check your configuration.");
}
?>