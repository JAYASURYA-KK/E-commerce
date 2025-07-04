<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["username"])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingType = isset($_POST['booking_type']) ? $_POST['booking_type'] : '';
    $bookingId = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
    $username = $_SESSION["username"];
    
    if (empty($bookingType) || empty($bookingId)) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit();
    }
    
    $success = false;
    
    if ($bookingType == 'hotel') {
        // Cancel hotel booking
        $cancelSQL = "UPDATE hotelbookings SET cancelled = 'yes' WHERE bookingID = ? AND username = ?";
        $stmt = $conn->prepare($cancelSQL);
        $stmt->bind_param("is", $bookingId, $username);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        // Cancel flight/train booking
        $cancelSQL = "UPDATE booking_amounts SET status = 'cancelled' WHERE booking_reference = ? AND username = ?";
        $stmt = $conn->prepare($cancelSQL);
        $stmt->bind_param("ss", $bookingId, $username);
        $success = $stmt->execute();
        $stmt->close();
    }
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>