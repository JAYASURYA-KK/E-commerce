<?php 
session_start();
if(!isset($_SESSION["username"]))
{
    header("Location:blocked.php");
    $_SESSION['url'] = $_SERVER['REQUEST_URI']; 
}

// Handle PDF generation
if(isset($_GET['download_pdf']) && isset($_GET['booking_id']) && isset($_GET['type'])) {
    generateTicketPDF($_GET['booking_id'], $_GET['type']);
    exit;
}

function generateQRCode($text, $size = 150) {
    // Using QR Server API (more reliable alternative)
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($text);
    return $qr_url;
}

function createQRCodePlaceholder($pdf, $x, $y, $size, $text) {
    // Fallback: Create a simple QR code placeholder if API fails
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Rect($x, $y, $size, $size);
    
    // Add grid pattern to simulate QR code
    $cellSize = $size / 10;
    for($i = 0; $i < 10; $i++) {
        for($j = 0; $j < 10; $j++) {
            if(($i + $j) % 2 == 0) {
                $pdf->SetFillColor(0, 0, 0);
                $pdf->Rect($x + ($i * $cellSize), $y + ($j * $cellSize), $cellSize, $cellSize, 'F');
            }
        }
    }
    
    // Add text below
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x, $y + $size + 2);
    $pdf->Cell($size, 5, 'QR CODE', 0, 0, 'C');
}

function generateTicketPDF($bookingId, $type) {
    // Include FPDF library
    require_once('fpdf/fpdf.php');
    
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "projectmeteor";
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Get booking details
    $user = $_SESSION["username"];
    if($type == 'flight') {
        $sql = "SELECT * FROM `flightbookings` WHERE bookingID='$bookingId' AND username='$user' AND cancelled='no'";
    } else {
        $sql = "SELECT * FROM `trainbookings` WHERE bookingID='$bookingId' AND username='$user' AND cancelled='no'";
    }
    
    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();
    
    if(!$booking) {
        die("Booking not found");
    }
    
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Company Header
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->Cell(0, 15, 'JS WEBY TOURISM', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, strtoupper($type) . ' E-TICKET', 0, 1, 'C');
    $pdf->Ln(10);
    
    // Ticket details box
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Rect(10, $pdf->GetY(), 190, 90, 'F');
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'BOOKING DETAILS', 0, 1, 'L');
    $pdf->Ln(5);
    
    // Booking details
    $pdf->SetFont('Arial', '', 11);
    
    // Booking ID
    $pdf->Cell(50, 8, 'Booking ID:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, $booking['bookingID'], 0, 1, 'L');
    
    // Passenger Name
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 8, 'Passenger:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, strtoupper($booking['username']), 0, 1, 'L');
    
    // Origin
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 8, 'From:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, $booking['origin'], 0, 1, 'L');
    
    // Destination
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 8, 'To:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, $booking['destination'], 0, 1, 'L');
    
    // Date
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 8, 'Travel Date:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, date('d M Y', strtotime($booking['date'])), 0, 1, 'L');
    
    // Flight specific details
    if($type == 'flight') {
        $flightType = ($booking['type'] == 'OneWayFlight') ? 'One Way' : 'Return Trip';
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(50, 8, 'Flight Type:', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, $flightType, 0, 1, 'L');
    }
    
    // Generation time
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 8, 'Generated:', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, date('d M Y H:i:s'), 0, 1, 'L');
    
    $pdf->Ln(15);
    
    // QR Code section
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, 'SCAN QR CODE FOR MOBILE TICKET', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Create mobile-friendly QR code URL
    $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $qrData = $baseUrl . "/verify-ticket.php?id=" . $booking['bookingID'] . "&type=" . $type . "&user=" . urlencode($booking['username']);
    
    // Try to get QR code image
    $qrX = 80;
    $qrY = $pdf->GetY();
    $qrSize = 40;
    
    try {
        // Try QR Server API first
        $qrUrl = generateQRCode($qrData, 150);
        
        // Set context options for file_get_contents
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $qrImageData = @file_get_contents($qrUrl, false, $context);
        
        if($qrImageData !== false) {
            $tempQrFile = 'temp_qr_' . $bookingId . '.png';
            file_put_contents($tempQrFile, $qrImageData);
            
            // Verify the image is valid
            if(getimagesize($tempQrFile)) {
                $pdf->Image($tempQrFile, $qrX, $qrY, $qrSize, $qrSize, 'PNG');
                unlink($tempQrFile); // Clean up
            } else {
                throw new Exception("Invalid image data");
            }
        } else {
            throw new Exception("Failed to fetch QR code");
        }
    } catch (Exception $e) {
        // Fallback: Create QR code placeholder
        createQRCodePlaceholder($pdf, $qrX, $qrY, $qrSize, $qrData);
    }
    
    $pdf->Ln(45);
    
    // Add verification code
    $verificationCode = strtoupper(substr(md5($booking['bookingID'] . $booking['username']), 0, 8));
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, 'Verification Code: ' . $verificationCode, 0, 1, 'C');
    
    // Add QR URL for manual access
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, 'Manual Verification: ' . $qrData, 0, 1, 'C');
    
    $pdf->Ln(10);
    
    // Footer with corrected information
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(128, 128, 128);
    $pdf->Cell(0, 5, 'Generated on: ' . date('d M Y H:i:s'), 0, 1, 'C');
    $pdf->Cell(0, 5, 'Thank you for choosing JS Weby', 0, 1, 'C');
    $pdf->Cell(0, 5, 'For support: JSweby@gmail.com | Phone: +91 9080418085', 0, 1, 'C');
    
    // Add terms and conditions
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, 'Terms: Please carry a valid ID proof during travel. Ticket is non-transferable.', 0, 1, 'C');
    $pdf->Cell(0, 4, 'This is a computer generated ticket and does not require signature.', 0, 1, 'C');
    
    // Output PDF
    $filename = $type . '_ticket_' . $bookingId . '.pdf';
    $pdf->Output('D', $filename); // 'D' forces download
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | JS Weby Tourism</title>

    <link href="css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-select.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Oswald:200,300,400|Raleway:100,300,400,500|Roboto:100,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/userDashboard.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/moment-with-locales.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>

    <style>
    .download-btn {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-weight: 600;
        box-shadow: 0 3px 6px rgba(0, 123, 255, 0.3);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }

    .download-btn:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
    }

    .download-btn i {
        margin-right: 8px;
        font-size: 14px;
    }

    .ticket-actions {
        text-align: center;
        padding: 8px;
    }

    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .table thead th {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 15px 10px;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
    }

    .table tbody td {
        padding: 12px 10px;
        vertical-align: middle;
        border-color: #e9ecef;
    }

    .noBooking {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .noBooking i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
        display: block;
    }

    .noBooking p {
        margin: 10px 0;
        font-size: 16px;
    }

    .noBooking small {
        color: #adb5bd;
    }

    .heading {
        color: #007bff;
        font-weight: 700;
        margin-bottom: 30px;
        font-size: 28px;
    }

    .tag {
        font-weight: 600;
        color: #495057;
    }

    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #28a745;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }
    </style>
</head>

<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "projectmeteor";
    
    // Creating a connection to MySQL database
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Checking if successfully connected to the database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<body>
    <div class="container-fluid">
        <div class="col-sm-12 userDashboard text-center">
            <?php include("common/headerDashboardTransparentLoggedIn.php"); ?>

            <div class="col-sm-12">
                <div class="heading text-center">
                    <span class="status-indicator"></span>My Dashboard
                </div>
            </div>

            <div class="col-sm-1"></div>

            <div class="col-sm-3 containerBoxLeft">
                <a href="userDashboardProfile.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-user-o"></span> My Profile
                    </div>
                </a>

                <a href="userDashboardBookings.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-copy"></span> My Bookings
                    </div>
                </a>

                <div class="col-sm-12 menuContainer bottomBorder active">
                    <span class="fa fa-clone"></span> My E-Tickets
                </div>

                <a href="userDashboardCancelTicket.php">
                    <div class="col-sm-12 menuContainer bottomBorder">
                        <span class="fa fa-close"></span> Cancel Ticket
                    </div>
                </a>

                <a href="userDashboardAccountSettings.php">
                    <div class="col-sm-12 menuContainer">
                        <span class="fa fa-bar-chart"></span> Account Stats
                    </div>
                </a>
            </div>

            <div class="col-sm-7 containerBoxRight text-left">
                <?php 
                    $user = $_SESSION["username"];
                    //flight bookings query
                    $flightBookingsSQL = "SELECT COUNT(*) FROM `flightbookings` WHERE Username='$user' AND cancelled='no'";
                    $flightBookingsQuery = $conn->query($flightBookingsSQL);
                    $noOfFlightBookings = $flightBookingsQuery->fetch_array(MYSQLI_NUM);
                     // train bookings query
                    $trainBookingsSQL = "SELECT COUNT(*) FROM `trainbookings` WHERE username='$user' AND cancelled='no'";
                    $trainBookingsQuery = $conn->query($trainBookingsSQL);
                    $noOfTrainBookings = $trainBookingsQuery->fetch_array(MYSQLI_NUM);
                ?>

                <div class="col-sm-12 tickets">
                    <div class="col-sm-6 ticketsWrapper topMargin">
                        <div class="tag">Select the type of ticket: </div>
                    </div>

                    <div class="col-sm-6 topMargin pullLeft">
                        <select class="input" name="ticketTypeSelector" id="ticketTypeSelector" />
                        <option value="flightTickets">✈️ Flight Tickets (<?php echo $noOfFlightBookings[0]; ?>)</option>
                        <option value="trainTickets">🚂 Train Tickets (<?php echo $noOfTrainBookings[0]; ?>)</option>
                        </select>
                    </div>

                    <?php if($noOfFlightBookings[0]>0): ?>
                    <!-- FLIGHT TICKETS SECTION STARTS -->
                    <div class="col-sm-12 ticketTableContainer pullABitLeft" id="flightTicketsWrapper">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Origin</th>
                                    <th class="text-center">Destination</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Download PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $flightTicketsSQL = "SELECT * FROM `flightbookings` WHERE username='$user' AND cancelled='no'";
                                $flightTicketsQuery = $conn->query($flightTicketsSQL);
                
                                while($flightTicketsRow = $flightTicketsQuery->fetch_assoc()) { 
                                    $modeDB=$flightTicketsRow["type"];
                                    if($modeDB=="OneWayFlight") {
                                        $modePrint="One Way";
                                    }
                                    else if($modeDB=="ReturnTripFlight") {
                                        $modePrint="Return Trip";
                                    }
                            ?>

                                <tr>
                                    <td class="text-center font-weight-bold">
                                        <?php echo $flightTicketsRow["bookingID"]; ?></td>
                                    <td class="text-center"><?php echo $flightTicketsRow["origin"]; ?></td>
                                    <td class="text-center"><?php echo $flightTicketsRow["destination"]; ?></td>
                                    <td class="text-center">
                                        <?php echo date('d M Y', strtotime($flightTicketsRow["date"])); ?></td>
                                    <td class="text-center"><?php echo $modePrint; ?></td>
                                    <td class="ticket-actions">
                                        <a href="?download_pdf=1&booking_id=<?php echo $flightTicketsRow["bookingID"]; ?>&type=flight"
                                            class="download-btn" title="Download PDF Ticket with QR Code">
                                            <i class="fa fa-file-pdf-o"></i> Download PDF
                                        </a>
                                    </td>
                                </tr>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php else: ?>
                    <div class="col-sm-12 ticketTableContainer" id="flightTicketsWrapper">
                        <div class="noBooking">
                            <i class="fa fa-plane"></i>
                            <p><strong>No Flight Bookings Found</strong></p>
                            <p>Looks like you haven't booked any flight with us yet.</p>
                            <p><small>Your flight tickets will appear here once you make a booking.</small></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($noOfTrainBookings[0]>0): ?>
                    <!-- TRAIN TICKETS SECTION STARTS -->
                    <div class="col-sm-12 ticketTableContainer pullABitLeft" id="trainTicketsWrapper">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Origin</th>
                                    <th class="text-center">Destination</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Download PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $trainTicketsSQL = "SELECT * FROM `trainbookings` WHERE username='$user' AND cancelled='no'";
                                $trainTicketsQuery = $conn->query($trainTicketsSQL);
                
                                while($trainTicketsRow = $trainTicketsQuery->fetch_assoc()) { 
                            ?>

                                <tr>
                                    <td class="text-center font-weight-bold">
                                        <?php echo $trainTicketsRow["bookingID"]; ?></td>
                                    <td class="text-center"><?php echo $trainTicketsRow["origin"]; ?></td>
                                    <td class="text-center"><?php echo $trainTicketsRow["destination"]; ?></td>
                                    <td class="text-center">
                                        <?php echo date('d M Y', strtotime($trainTicketsRow["date"])); ?></td>
                                    <td class="ticket-actions">
                                        <a href="?download_pdf=1&booking_id=<?php echo $trainTicketsRow["bookingID"]; ?>&type=train"
                                            class="download-btn" title="Download PDF Ticket with QR Code">
                                            <i class="fa fa-file-pdf-o"></i> Download PDF
                                        </a>
                                    </td>
                                </tr>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <?php else: ?>
                    <div class="col-sm-12 ticketTableContainer" id="trainTicketsWrapper">
                        <div class="noBooking">
                            <i class="fa fa-train"></i>
                            <p><strong>No Train Bookings Found</strong></p>
                            <p>Looks like you haven't booked any train with us yet.</p>
                            <p><small>This area will list all your train bookings once you start booking trains.</small>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div> <!-- containerBoxRight -->

            <div class="col-sm-1"></div>
            <div class="col-sm-12 spacer">a</div>
            <div class="col-sm-12 spacer">a</div>
        </div>
    </div> <!-- container-fluid -->

    <?php include("common/footer.php"); ?>
</body>

</html>