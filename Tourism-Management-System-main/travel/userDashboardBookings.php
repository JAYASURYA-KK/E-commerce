<?php 
session_start();
if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
    $_SESSION['url'] = $_SERVER['REQUEST_URI']; 
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Bookings Dashboard | Tourism Management</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <style>
    .booking-tabs {
        margin: 20px 0;
    }

    .booking-tabs .nav-tabs {
        border-bottom: 2px solid #ddd;
    }

    .booking-tabs .nav-tabs>li>a {
        border: none;
        border-radius: 0;
        color: #666;
        font-weight: bold;
        padding: 15px 25px;
    }

    .booking-tabs .nav-tabs>li.active>a {
        background: #007bff;
        color: white;
        border: none;
    }

    .booking-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 15px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .booking-header {
        background: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        border-radius: 8px 8px 0 0;
    }

    .booking-content {
        padding: 15px;
    }

    .booking-actions {
        padding: 10px 15px;
        background: #f8f9fa;
        border-top: 1px solid #ddd;
        border-radius: 0 0 8px 8px;
        text-align: right;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .booking-detail {
        margin: 5px 0;
    }

    .booking-detail strong {
        color: #333;
        min-width: 120px;
        display: inline-block;
    }

    .no-bookings {
        text-align: center;
        padding: 50px;
        color: #666;
    }

    .no-bookings i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ddd;
    }

    .download-btn {
        margin-right: 5px;
    }
    </style>

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="col-sm-12 userDashboard text-center">

            <?php include("common/headerDashboardTransparentLoggedIn.php"); ?>

            <div class="col-sm-12">
                <div class="heading">
                    My Dashboard
                </div>
            </div>

            <div class="col-sm-1"></div>

            <!-- Sidebar Menu -->
            <div class="col-sm-3 containerBoxLeft">
                <a href="userDashboardProfile.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-user-o"></span> My Profile
                    </div>
                </a>

                <div class="col-sm-12 menuContainer bottomBorder active">
                    <span class="fa fa-copy"></span> My Bookings
                </div>

                <a href="userDashboardETickets.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-clone"></span> My E-Tickets
                    </div>
                </a>

                <a href="userDashboardCancelTicket.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-close"></span> Cancel Ticket
                    </div>
                </a>

                <a href="userDashboardAccountSettings.php">
                    <div class="col-sm-12 menuContainer noBottomBorder">
                        <span class="fa fa-bar-chart"></span> Account Stats
                    </div>
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-sm-7 containerBoxRightHotel text-left">

                <!-- Booking Tabs -->
                <div class="booking-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#hotels" aria-controls="hotels" role="tab" data-toggle="tab">
                                <i class="fa fa-building"></i> Hotels
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#flights" aria-controls="flights" role="tab" data-toggle="tab">
                                <i class="fa fa-plane"></i> Flights
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#trains" aria-controls="trains" role="tab" data-toggle="tab">
                                <i class="fa fa-train"></i> Trains
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#all" aria-controls="all" role="tab" data-toggle="tab">
                                <i class="fa fa-list"></i> All Bookings
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">

                    <!-- Hotel Bookings Tab -->
                    <div role="tabpanel" class="tab-pane active" id="hotels">
                        <?php
                        $user = $_SESSION["username"];
                        
                        // Get hotel bookings
                        $hotelBookingsSQL = "SELECT * FROM hotelbookings WHERE username = ? ORDER BY bookingID DESC";
                        $stmt = $conn->prepare($hotelBookingsSQL);
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $hotelResult = $stmt->get_result();
                        
                        if ($hotelResult && $hotelResult->num_rows > 0):
                        ?>
                        <?php while($hotel = $hotelResult->fetch_assoc()): ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 style="margin: 0;">
                                            <i class="fa fa-building"></i> Hotel Booking
                                            #<?php echo $hotel['bookingID']; ?>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <span
                                            class="status-badge <?php echo $hotel['cancelled'] == 'yes' ? 'status-cancelled' : 'status-confirmed'; ?>">
                                            <?php echo $hotel['cancelled'] == 'yes' ? 'Cancelled' : 'Confirmed'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-content">
                                <div class="booking-detail">
                                    <strong>Hotel:</strong> <?php echo htmlspecialchars($hotel['hotelName']); ?>
                                </div>
                                <div class="booking-detail">
                                    <strong>Booking Date:</strong> <?php echo htmlspecialchars($hotel['date']); ?>
                                </div>
                                <div class="booking-detail">
                                    <strong>Status:</strong>
                                    <?php echo $hotel['cancelled'] == 'yes' ? 'Cancelled' : 'Active'; ?>
                                </div>
                            </div>

                            <div class="booking-actions">
                                <a href="generate_pdf.php?type=hotel&id=<?php echo $hotel['bookingID']; ?>"
                                    class="btn btn-primary btn-sm download-btn" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF
                                </a>

                                <?php 
                                        $receiptFile = 'receipts/hotel_receipt_' . $hotel['bookingID'] . '.html';
                                        if (file_exists($receiptFile)): 
                                        ?>
                                <a href="<?php echo $receiptFile; ?>" target="_blank"
                                    class="btn btn-info btn-sm download-btn">
                                    <i class="fa fa-eye"></i> View Receipt
                                </a>
                                <?php endif; ?>

                                <?php if ($hotel['cancelled'] == 'no'): ?>
                                <button onclick="cancelBooking('hotel', <?php echo $hotel['bookingID']; ?>)"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="no-bookings">
                            <i class="fa fa-building"></i>
                            <h4>No Hotel Bookings</h4>
                            <p>You haven't booked any hotels yet.</p>
                            <a href="hotels.php" class="btn btn-primary">Book a Hotel</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Flight Bookings Tab -->
                    <div role="tabpanel" class="tab-pane" id="flights">
                        <?php
                        // Get flight bookings from booking_amounts table
                        $flightBookingsSQL = "SELECT * FROM booking_amounts WHERE username = ? AND booking_type = 'flight' ORDER BY created_at DESC";
                        $stmt = $conn->prepare($flightBookingsSQL);
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $flightResult = $stmt->get_result();
                        
                        if ($flightResult && $flightResult->num_rows > 0):
                        ?>
                        <?php while($flight = $flightResult->fetch_assoc()): ?>
                        <?php $flightDetails = json_decode($flight['booking_details'], true); ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 style="margin: 0;">
                                            <i class="fa fa-plane"></i> Flight Booking
                                            <?php echo $flight['booking_reference']; ?>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <span class="status-badge status-<?php echo $flight['status']; ?>">
                                            <?php echo ucfirst($flight['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="booking-detail">
                                            <strong>Route:</strong>
                                            <?php echo htmlspecialchars($flight['origin'] . ' → ' . $flight['destination']); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Travel Date:</strong>
                                            <?php echo htmlspecialchars($flight['travel_date']); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Passengers:</strong> <?php echo $flight['passengers']; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="booking-detail">
                                            <strong>Flight Type:</strong>
                                            <?php echo isset($flightDetails['type']) ? ucfirst($flightDetails['type']) : 'N/A'; ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Class:</strong>
                                            <?php echo isset($flightDetails['class']) ? $flightDetails['class'] : 'N/A'; ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Total Amount:</strong> ₹
                                            <?php echo number_format($flight['total_amount'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-actions">
                                <a href="generate_pdf.php?type=flight&ref=<?php echo $flight['booking_reference']; ?>"
                                    class="btn btn-primary btn-sm download-btn" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF
                                </a>

                                <?php if ($flight['status'] == 'confirmed'): ?>
                                <button onclick="cancelBooking('flight', '<?php echo $flight['booking_reference']; ?>')"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="no-bookings">
                            <i class="fa fa-plane"></i>
                            <h4>No Flight Bookings</h4>
                            <p>You haven't booked any flights yet.</p>
                            <a href="flights.php" class="btn btn-primary">Book a Flight</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Train Bookings Tab -->
                    <div role="tabpanel" class="tab-pane" id="trains">
                        <?php
                        // Get train bookings from booking_amounts table
                        $trainBookingsSQL = "SELECT * FROM booking_amounts WHERE username = ? AND booking_type = 'train' ORDER BY created_at DESC";
                        $stmt = $conn->prepare($trainBookingsSQL);
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $trainResult = $stmt->get_result();
                        
                        if ($trainResult && $trainResult->num_rows > 0):
                        ?>
                        <?php while($train = $trainResult->fetch_assoc()): ?>
                        <?php $trainDetails = json_decode($train['booking_details'], true); ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 style="margin: 0;">
                                            <i class="fa fa-train"></i> Train Booking
                                            <?php echo $train['booking_reference']; ?>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <span class="status-badge status-<?php echo $train['status']; ?>">
                                            <?php echo ucfirst($train['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="booking-detail">
                                            <strong>Route:</strong>
                                            <?php echo htmlspecialchars($train['origin'] . ' → ' . $train['destination']); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Travel Date:</strong>
                                            <?php echo htmlspecialchars($train['travel_date']); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Passengers:</strong> <?php echo $train['passengers']; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="booking-detail">
                                            <strong>Train:</strong>
                                            <?php echo isset($trainDetails['train_name']) ? $trainDetails['train_name'] : 'N/A'; ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Class:</strong>
                                            <?php echo isset($trainDetails['class']) ? $trainDetails['class'] : 'N/A'; ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Total Amount:</strong> ₹
                                            <?php echo number_format($train['total_amount'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-actions">
                                <a href="generate_pdf.php?type=train&ref=<?php echo $train['booking_reference']; ?>"
                                    class="btn btn-primary btn-sm download-btn" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF
                                </a>

                                <?php if ($train['status'] == 'confirmed'): ?>
                                <button onclick="cancelBooking('train', '<?php echo $train['booking_reference']; ?>')"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="no-bookings">
                            <i class="fa fa-train"></i>
                            <h4>No Train Bookings</h4>
                            <p>You haven't booked any trains yet.</p>
                            <a href="trains.php" class="btn btn-primary">Book a Train</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- All Bookings Tab -->
                    <div role="tabpanel" class="tab-pane" id="all">
                        <?php
                        // Get all bookings from booking_amounts table
                        $allBookingsSQL = "SELECT * FROM booking_amounts WHERE username = ? ORDER BY created_at DESC";
                        $stmt = $conn->prepare($allBookingsSQL);
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $allResult = $stmt->get_result();
                        
                        if ($allResult && $allResult->num_rows > 0):
                        ?>
                        <?php while($booking = $allResult->fetch_assoc()): ?>
                        <?php 
                                $details = json_decode($booking['booking_details'], true);
                                $icon = $booking['booking_type'] == 'flight' ? 'plane' : ($booking['booking_type'] == 'hotel' ? 'building' : 'train');
                                ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <h4 style="margin: 0;">
                                            <i class="fa fa-<?php echo $icon; ?>"></i>
                                            <?php echo ucfirst($booking['booking_type']); ?> Booking
                                            <?php echo $booking['booking_reference']; ?>
                                        </h4>
                                    </div>
                                    <div class="col-sm-4 text-right">
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-content">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php if ($booking['origin'] && $booking['destination']): ?>
                                        <div class="booking-detail">
                                            <strong>Route:</strong>
                                            <?php echo htmlspecialchars($booking['origin'] . ' → ' . $booking['destination']); ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="booking-detail">
                                            <strong>Travel Date:</strong>
                                            <?php echo htmlspecialchars($booking['travel_date']); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Passengers:</strong> <?php echo $booking['passengers']; ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="booking-detail">
                                            <strong>Booking Date:</strong>
                                            <?php echo date('d M Y', strtotime($booking['created_at'])); ?>
                                        </div>
                                        <div class="booking-detail">
                                            <strong>Total Amount:</strong> ₹
                                            <?php echo number_format($booking['total_amount'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-actions">
                                <a href="generate_pdf.php?type=<?php echo $booking['booking_type']; ?>&ref=<?php echo $booking['booking_reference']; ?>"
                                    class="btn btn-primary btn-sm download-btn" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF
                                </a>

                                <?php if ($booking['status'] == 'confirmed'): ?>
                                <button
                                    onclick="cancelBooking('<?php echo $booking['booking_type']; ?>', '<?php echo $booking['booking_reference']; ?>')"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-times"></i> Cancel
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="no-bookings">
                            <i class="fa fa-list"></i>
                            <h4>No Bookings Found</h4>
                            <p>You haven't made any bookings yet.</p>
                            <a href="index.php" class="btn btn-primary">Start Booking</a>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="col-sm-1"></div>

            <div class="col-sm-12 spacer">a</div>
            <div class="col-sm-12 spacer">a</div>
        </div>
    </div>

    <?php include("common/footer.php"); ?>

    <script>
    function cancelBooking(type, id) {
        if (confirm('Are you sure you want to cancel this booking?')) {
            $.ajax({
                url: 'cancel_booking_enhanced.php',
                method: 'POST',
                data: {
                    booking_type: type,
                    booking_id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Booking cancelled successfully');
                        location.reload();
                    } else {
                        alert('Error cancelling booking: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error cancelling booking');
                }
            });
        }
    }
    </script>

    <?php $conn->close(); ?>
</body>

</html>