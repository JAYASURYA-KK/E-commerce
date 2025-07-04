<?php
session_start();
if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourism_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate booking reference
function generateBookingReference($type) {
    $prefix = strtoupper(substr($type, 0, 2));
    return $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get common booking data
    $booking_type = $_POST['booking_type'] ?? '';
    $total_price = (float)($_POST['total_price'] ?? 0);
    $base_fare = (float)($_POST['base_fare'] ?? 0);
    $convenience_fee = 250.00;
    
    // Generate booking reference
    $booking_reference = generateBookingReference($booking_type);
    
    // Prepare booking data array
    $bookingData = [
        'user_id' => $_SESSION['user_id'] ?? 1, // Assuming user_id is stored in session
        'username' => $_SESSION['username'],
        'booking_type' => $booking_type,
        'booking_reference' => $booking_reference,
        'total_price' => $total_price,
        'base_fare' => $base_fare,
        'convenience_fee' => $convenience_fee,
        'booking_date' => date('Y-m-d'),
        'status' => 'confirmed'
    ];
    
    // Initialize all fields to NULL
    $bookingData['origin'] = NULL;
    $bookingData['destination'] = NULL;
    $bookingData['travel_date'] = NULL;
    $bookingData['return_date'] = NULL;
    $bookingData['adults'] = 0;
    $bookingData['children'] = 0;
    $bookingData['total_passengers'] = 0;
    $bookingData['flight_type'] = NULL;
    $bookingData['flight_class'] = NULL;
    $bookingData['outbound_flight_no'] = NULL;
    $bookingData['inbound_flight_no'] = NULL;
    $bookingData['hotel_id'] = NULL;
    $bookingData['hotel_name'] = NULL;
    $bookingData['check_in_date'] = NULL;
    $bookingData['check_out_date'] = NULL;
    $bookingData['number_of_rooms'] = NULL;
    $bookingData['number_of_guests'] = NULL;
    $bookingData['number_of_nights'] = NULL;
    $bookingData['train_id'] = NULL;
    $bookingData['train_name'] = NULL;
    $bookingData['train_class'] = NULL;
    
    // Set specific data based on booking type
    if ($booking_type == 'flight') {
        $bookingData['origin'] = $_POST['origin'] ?? '';
        $bookingData['destination'] = $_POST['destination'] ?? '';
        $bookingData['travel_date'] = $_POST['travel_date'] ?? '';
        $bookingData['return_date'] = $_POST['return_date'] ?? NULL;
        $bookingData['adults'] = (int)($_POST['adults'] ?? 0);
        $bookingData['children'] = (int)($_POST['children'] ?? 0);
        $bookingData['total_passengers'] = $bookingData['adults'] + $bookingData['children'];
        $bookingData['flight_type'] = $_POST['flight_type'] ?? '';
        $bookingData['flight_class'] = $_POST['flight_class'] ?? '';
        $bookingData['outbound_flight_no'] = $_POST['outbound_flight_no'] ?? '';
        $bookingData['inbound_flight_no'] = $_POST['inbound_flight_no'] ?? NULL;
    } 
    elseif ($booking_type == 'hotel') {
        $bookingData['hotel_id'] = (int)($_POST['hotel_id'] ?? 0);
        $bookingData['hotel_name'] = $_POST['hotel_name'] ?? '';
        $bookingData['check_in_date'] = $_POST['check_in_date'] ?? '';
        $bookingData['check_out_date'] = $_POST['check_out_date'] ?? '';
        $bookingData['number_of_rooms'] = (int)($_POST['number_of_rooms'] ?? 0);
        $bookingData['number_of_guests'] = (int)($_POST['number_of_guests'] ?? 0);
        $bookingData['number_of_nights'] = (int)($_POST['number_of_nights'] ?? 0);
        $bookingData['travel_date'] = $bookingData['check_in_date'];
    } 
    elseif ($booking_type == 'train') {
        $bookingData['origin'] = $_POST['origin'] ?? '';
        $bookingData['destination'] = $_POST['destination'] ?? '';
        $bookingData['travel_date'] = $_POST['travel_date'] ?? '';
        $bookingData['train_id'] = $_POST['train_id'] ?? '';
        $bookingData['train_name'] = $_POST['train_name'] ?? '';
        $bookingData['train_class'] = $_POST['train_class'] ?? '';
        $bookingData['total_passengers'] = (int)($_POST['total_passengers'] ?? 0);
    }
    
    // Insert booking into database
    $sql = "INSERT INTO bookings (
        user_id, username, booking_type, booking_reference, total_price, base_fare, convenience_fee,
        origin, destination, booking_date, travel_date, return_date, adults, children, total_passengers,
        flight_type, flight_class, outbound_flight_no, inbound_flight_no,
        hotel_id, hotel_name, check_in_date, check_out_date, number_of_rooms, number_of_guests, number_of_nights,
        train_id, train_name, train_class, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isssddsssssiiiisssssisssiisss", 
            $bookingData['user_id'], $bookingData['username'], $bookingData['booking_type'], 
            $bookingData['booking_reference'], $bookingData['total_price'], $bookingData['base_fare'], 
            $bookingData['convenience_fee'], $bookingData['origin'], $bookingData['destination'], 
            $bookingData['booking_date'], $bookingData['travel_date'], $bookingData['return_date'], 
            $bookingData['adults'], $bookingData['children'], $bookingData['total_passengers'],
            $bookingData['flight_type'], $bookingData['flight_class'], $bookingData['outbound_flight_no'], 
            $bookingData['inbound_flight_no'], $bookingData['hotel_id'], $bookingData['hotel_name'], 
            $bookingData['check_in_date'], $bookingData['check_out_date'], $bookingData['number_of_rooms'], 
            $bookingData['number_of_guests'], $bookingData['number_of_nights'], $bookingData['train_id'], 
            $bookingData['train_name'], $bookingData['train_class'], $bookingData['status']
        );
        
        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            $success = true;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Confirmation | Tourism Management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <?php include("common/headerLoggedIn.php"); ?>

    <div class="spacer">a</div>

    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <?php if (isset($success) && $success): ?>
                <div class="alert alert-success text-center">
                    <h2><i class="fa fa-check-circle"></i> Booking Confirmed!</h2>
                    <p><strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking_reference); ?></p>
                    <p><strong>Total Amount:</strong> ₹ <?php echo number_format($total_price, 2); ?></p>
                    <p><strong>Booking Type:</strong> <?php echo ucfirst(htmlspecialchars($booking_type)); ?></p>
                    <p>Your booking has been successfully confirmed. You will receive a confirmation email shortly.</p>
                    <br>
                    <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
                    <a href="index.php" class="btn btn-default">Back to Home</a>
                </div>
                <?php elseif (isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <h2><i class="fa fa-exclamation-triangle"></i> Booking Failed!</h2>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <br>
                    <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center">
                    <h2><i class="fa fa-exclamation-triangle"></i> Invalid Request!</h2>
                    <p>No booking data received.</p>
                    <br>
                    <a href="index.php" class="btn btn-primary">Back to Home</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="spacerLarge">.</div>

    <?php include("common/footer.php"); ?>
</body>

</html>