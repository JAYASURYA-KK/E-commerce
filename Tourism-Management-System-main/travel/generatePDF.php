<?php
session_start();
require_once('tcpdf/tcpdf.php');

if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
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

$user = $_SESSION["username"];
$ticketType = $_GET['type'] ?? '';
$bookingID = $_GET['id'] ?? '';

if (empty($ticketType) || empty($bookingID)) {
    die("Invalid parameters");
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Tourism Management System');
$pdf->SetAuthor('Tourism Management');
$pdf->SetTitle('E-Ticket');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

if ($ticketType === 'flight') {
    // Get flight booking details
    $sql = "SELECT * FROM flightbookings WHERE bookingID='$bookingID' AND username='$user' AND cancelled='no'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $modeDB = $row["type"];
        $modePrint = ($modeDB == "OneWayFlight") ? "One Way" : "Return Trip";
        
        // Create flight ticket content
        $html = '
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #2c3e50;">FLIGHT E-TICKET</h1>
            <hr style="border: 2px solid #3498db;">
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Booking ID:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["bookingID"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Passenger Name:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["username"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Origin:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["origin"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Destination:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["destination"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Date:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["date"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Type:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $modePrint . '</td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; text-align: center; color: #7f8c8d;">
            <p>Thank you for choosing our service!</p>
            <p>Please present this ticket at the airport.</p>
        </div>';
        
        $filename = 'Flight_Ticket_' . $bookingID . '.pdf';
    }
} elseif ($ticketType === 'train') {
    // Get train booking details
    $sql = "SELECT * FROM trainbookings WHERE bookingID='$bookingID' AND username='$user' AND cancelled='no'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Create train ticket content
        $html = '
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #2c3e50;">TRAIN E-TICKET</h1>
            <hr style="border: 2px solid #e74c3c;">
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Booking ID:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["bookingID"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Passenger Name:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["username"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Origin:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["origin"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Destination:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["destination"] . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;"><strong>Date:</strong></td>
                <td style="border: 1px solid #ddd; padding: 10px;">' . $row["date"] . '</td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; text-align: center; color: #7f8c8d;">
            <p>Thank you for choosing our service!</p>
            <p>Please present this ticket at the railway station.</p>
        </div>';
        
        $filename = 'Train_Ticket_' . $bookingID . '.pdf';
    }
}

if (isset($html)) {
    // Write HTML content
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Output PDF
    $pdf->Output($filename, 'D'); // 'D' forces download
} else {
    die("Ticket not found or access denied");
}

$conn->close();
?>