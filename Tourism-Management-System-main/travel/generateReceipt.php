<?php 
session_start();
if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
    $_SESSION['url'] = $_SERVER['REQUEST_URI']; 
    exit();
}

// Start output buffering for receipt generation
ob_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set timezone and get current date
date_default_timezone_set("Asia/Kolkata");
$date = date('l jS \of F Y \a\t h:i:s A');

// Get booking data from POST or session
$hotelID = isset($_POST["hotelIDHidden"]) ? (int)$_POST["hotelIDHidden"] : 0;
$fare = isset($_POST["fareHidden"]) ? (float)$_POST["fareHidden"] : 0;
$bookingReference = isset($_POST["booking_reference"]) ? $_POST["booking_reference"] : '';

// If no POST data, try to get from latest booking
if (!$hotelID || !$fare) {
    $latestBookingSQL = "SELECT * FROM booking_amounts WHERE username = ? AND booking_type = 'hotel' ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($latestBookingSQL);
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $latestResult = $stmt->get_result();
    
    if ($latestResult && $latestResult->num_rows > 0) {
        $latestBooking = $latestResult->fetch_assoc();
        $fare = $latestBooking['total_amount'];
        $bookingReference = $latestBooking['booking_reference'];
        
        // Extract hotel ID from booking details
        $bookingDetails = json_decode($latestBooking['booking_details'], true);
        if (isset($bookingDetails['hotel_id'])) {
            $hotelID = $bookingDetails['hotel_id'];
        }
    }
    $stmt->close();
}

// Get hotel details
$hotelSQL = "SELECT * FROM hotels WHERE hotelID = ?";
$stmt = $conn->prepare($hotelSQL);
$stmt->bind_param("i", $hotelID);
$stmt->execute();
$hotelResult = $stmt->get_result();
$row = $hotelResult->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Hotel not found");
}

// Function to generate booking reference if not provided
function generateBookingReference() {
    return 'HT' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}

if (empty($bookingReference)) {
    $bookingReference = generateBookingReference();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hotel Booking Receipt | Tourism Management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .receipt-header {
        text-align: center;
        border-bottom: 2px solid #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .receipt-title {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .receipt-date {
        color: #666;
        font-size: 14px;
    }

    .booking-ref {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        color: #28a745;
    }

    .info-section {
        margin: 30px 0;
    }

    .section-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    .info-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
    }

    .info-label {
        font-weight: bold;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        color: #333;
        font-weight: bold;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .payment-table th,
    .payment-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    .payment-table th {
        background: #f8f9fa;
        font-weight: bold;
    }

    .total-amount {
        font-size: 20px;
        font-weight: bold;
        color: #28a745;
    }

    .important-info {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 5px;
        padding: 20px;
        margin: 30px 0;
    }

    .important-info h4 {
        color: #856404;
        margin-bottom: 15px;
    }

    .important-info ul {
        margin: 0;
        padding-left: 20px;
    }

    .important-info li {
        margin: 10px 0;
        color: #856404;
    }

    @media print {
        .no-print {
            display: none;
        }

        .receipt-container {
            box-shadow: none;
        }
    }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-title">Hotel Booking Receipt</div>
            <div class="receipt-date">Generated: <?php echo htmlspecialchars($date); ?></div>
        </div>

        <!-- Booking Reference -->
        <div class="booking-ref">
            Booking Reference: <?php echo htmlspecialchars($bookingReference); ?>
        </div>

        <!-- Hotel Information -->
        <div class="info-section">
            <div class="section-title">Hotel Information</div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Hotel ID</div>
                    <div class="info-value"><?php echo htmlspecialchars($row["hotelID"]); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Hotel Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($row["hotelName"]); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div class="info-value"><?php echo htmlspecialchars($row["locality"] . ', ' . $row["city"]); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="info-section">
            <div class="section-title">Booking Details</div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Check In</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SESSION["checkIn"] ?? 'N/A'); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Check Out</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SESSION["checkOut"] ?? 'N/A'); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Number of Rooms</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SESSION["noOfRooms"] ?? '1'); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Number of Guests</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SESSION["noOfGuests"] ?? '1'); ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Guest Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($_SESSION["username"]); ?></div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="info-section">
            <div class="section-title">Payment Information</div>

            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Rate per Room</th>
                        <th>Rooms</th>
                        <th>Nights</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $checkIn = $_SESSION["checkIn"] ?? '';
                    $checkOut = $_SESSION["checkOut"] ?? '';
                    $noOfRooms = $_SESSION["noOfRooms"] ?? 1;
                    
                    // Calculate nights
                    $nights = 1;
                    if (!empty($checkIn) && !empty($checkOut)) {
                        $date1 = date_create(str_replace('/', '-', $checkIn));
                        $date2 = date_create(str_replace('/', '-', $checkOut));
                        if ($date1 && $date2) {
                            $diff = date_diff($date1, $date2);
                            $nights = $diff->format("%a");
                        }
                    }
                    
                    $roomTotal = $row["price"] * $noOfRooms * $nights;
                    $convenienceFee = 250;
                    $totalAmount = $roomTotal + $convenienceFee;
                    ?>

                    <tr>
                        <td>Room Charges</td>
                        <td>₹ <?php echo number_format($row["price"], 2); ?></td>
                        <td><?php echo $noOfRooms; ?></td>
                        <td><?php echo $nights; ?></td>
                        <td>₹ <?php echo number_format($roomTotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Convenience Fee</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>₹ <?php echo number_format($convenienceFee, 2); ?></td>
                    </tr>
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="4">Total Amount Paid</td>
                        <td class="total-amount">₹ <?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4">Payment Mode</td>
                        <td>Card Payment</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Important Information -->
        <div class="important-info">
            <h4>Important Information</h4>
            <ul>
                <li>A printed copy of this receipt must be presented at the time of check-in.</li>
                <li>It is mandatory to have a Government recognised photo identification (ID) when checking-in. This can
                    include: Driving License, Passport, PAN Card, Voter ID Card or any other ID issued by the Government
                    of India.</li>
                <li>Check-in time is usually 2:00 PM and check-out time is 12:00 PM (noon).</li>
                <li>Any cancellation or modification requests should be made at least 24 hours before check-in.</li>
                <li>This booking is confirmed and payment has been processed successfully.</li>
            </ul>
        </div>

        <!-- Print Button -->
        <div class="text-center no-print" style="margin: 30px 0;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Receipt
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fa fa-home"></i> Back to Home
            </a>
        </div>
    </div>

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Save booking to database
$user = $_SESSION["username"];
$dateFormatted = date("d-m-Y");
$hotelName = $row["hotelName"] . ', ' . $row["locality"] . ', ' . $row["city"];

// Insert into hotelbookings table (original table)
$bookingDataInsertSQL = "INSERT INTO hotelbookings (hotelName, date, username, cancelled) VALUES (?, ?, ?, 'no')";
$stmt = $conn->prepare($bookingDataInsertSQL);
$stmt->bind_param("sss", $hotelName, $dateFormatted, $user);
$stmt->execute();
$stmt->close();

// Get the latest booking ID
$bookingIDSQL = "SELECT bookingID FROM hotelbookings ORDER BY bookingID DESC LIMIT 1";
$bookingIDResult = $conn->query($bookingIDSQL);
$bookingIDRow = $bookingIDResult->fetch_assoc();
$currentBookingID = $bookingIDRow['bookingID'];

// Update booking_amounts table status to confirmed
if (!empty($bookingReference)) {
    $updateAmountSQL = "UPDATE booking_amounts SET status = 'confirmed' WHERE booking_reference = ?";
    $stmt = $conn->prepare($updateAmountSQL);
    $stmt->bind_param("s", $bookingReference);
    $stmt->execute();
    $stmt->close();
}

// Create receipts directory if it doesn't exist
$receiptsDir = 'receipts';
if (!is_dir($receiptsDir)) {
    mkdir($receiptsDir, 0755, true);
}

// Save receipt as HTML file
$receiptContent = ob_get_contents();
$receiptFileName = $receiptsDir . '/hotel_receipt_' . $currentBookingID . '.html';
file_put_contents($receiptFileName, $receiptContent);

// Close database connection
$conn->close();

// End output buffering and display the receipt
ob_end_flush();
?>