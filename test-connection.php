<?php
// Test database connections and table structure
session_start();

// Set a test user ID if not logged in (for testing purposes)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 11; // Use the user ID from your screenshots
    $_SESSION['username'] = 'test_user';
}

echo "<h2>Database Connection Test</h2>";

// Test database connections
$db_food = new mysqli("localhost", "your_username", "your_password", "onlinefoodphp");
$db_travel = new mysqli("localhost", "your_username", "your_password", "projectmeteor");

if ($db_food->connect_error) {
    echo "<p style='color: red;'>❌ Food database connection failed: " . $db_food->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Food database connected successfully</p>";
    
    // Test users_orders table
    $result = $db_food->query("SHOW TABLES LIKE 'users_orders'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ users_orders table exists</p>";
        
        // Check for today's orders for user 11
        $today = date('Y-m-d');
        $check_orders = $db_food->query("SELECT COUNT(*) as count FROM users_orders WHERE u_id = 11 AND DATE(date) >= '$today'");
        if ($check_orders) {
            $order_count = $check_orders->fetch_assoc()['count'];
            echo "<p>📊 Orders for user 11 today: $order_count</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ users_orders table not found</p>";
    }
    
    // Test orders table
    $result = $db_food->query("SHOW TABLES LIKE 'orders'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ orders table exists</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ orders table not found</p>";
    }
}

if ($db_travel->connect_error) {
    echo "<p style='color: red;'>❌ Travel database connection failed: " . $db_travel->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Travel database connected successfully</p>";
    
    // Test booking_amounts table
    $result = $db_travel->query("SHOW TABLES LIKE 'booking_amounts'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ booking_amounts table exists</p>";
        
        // Check for today's bookings for user 11
        $today = date('Y-m-d');
        $check_bookings = $db_travel->query("SELECT COUNT(*) as count FROM booking_amounts WHERE user_id = 11");
        if ($check_bookings) {
            $booking_count = $check_bookings->fetch_assoc()['count'];
            echo "<p>📊 Bookings for user 11: $booking_count</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ booking_amounts table not found</p>";
    }
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Update the database credentials in config.php</li>";
echo "<li>Make sure your database tables exist</li>";
echo "<li>Test the spin wheel functionality</li>";
echo "</ol>";

echo "<p><a href='spin-wheel.php' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Spin Wheel</a></p>";
?>