<?php
session_start();
if(!isset($_SESSION["username"])) {
    header("Location:blocked.php");
    exit();
}

// Include FPDF library
require_once('fpdf/fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$ref = isset($_GET['ref']) ? $_GET['ref'] : '';

// Create PDF class
class BookingPDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Tourism Management System', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Booking Receipt', 0, 1, 'C');
        $this->Ln(5);
        
        // Add line
        $this->Line(10, 30, 200, 30);
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Generated on ' . date('d M Y, h:i A'), 0, 0, 'C');
    }
    
    function BookingInfo($title, $data) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $title, 0, 1, 'L');
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        $this->SetFont('Arial', '', 10);
        foreach($data as $key => $value) {
            $this->Cell(50, 6, $key . ':', 0, 0, 'L');
            $this->Cell(0, 6, $value, 0, 1, 'L');
        }
        $this->Ln(5);
    }
    
    function PaymentInfo($data) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'Payment Information', 0, 1, 'L');
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        // Table header
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(80, 8, 'Description', 1, 0, 'C');
        $this->Cell(40, 8, 'Amount', 1, 0, 'C');
        $this->Cell(40, 8, 'Status', 1, 1, 'C');
        
        // Table content
        $this->SetFont('Arial', '', 10);
        foreach($data as $row) {
            $this->Cell(80, 8, $row['description'], 1, 0, 'L');
            $this->Cell(40, 8, $row['amount'], 1, 0, 'R');
            $this->Cell(40, 8, $row['status'], 1, 1, 'C');
        }
        $this->Ln(5);
    }
}

// Initialize PDF
$pdf = new BookingPDF();
$pdf->AddPage();

if ($type == 'hotel' && !empty($id)) {
    // Hotel booking PDF
    $hotelSQL = "SELECT h.*, hb.* FROM hotelbookings hb 
                 LEFT JOIN hotels h ON h.hotelID = hb.bookingID 
                 WHERE hb.bookingID = ? AND hb.username = ?";
    $stmt = $conn->prepare($hotelSQL);
    $stmt->bind_param("is", $id, $_SESSION["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if ($booking) {
        // Get hotel details
        $hotelDetailsSQL = "SELECT * FROM hotels LIMIT 1"; // Get any hotel for demo
        $hotelResult = $conn->query($hotelDetailsSQL);
        $hotel = $hotelResult->fetch_assoc();
        
        $bookingInfo = [
            'Booking ID' => $booking['bookingID'],
            'Hotel Name' => $booking['hotelName'],
            'Booking Date' => $booking['date'],
            'Guest Name' => $booking['username'],
            'Status' => $booking['cancelled'] == 'yes' ? 'Cancelled' : 'Confirmed'
        ];
        
        $paymentInfo = [
            ['description' => 'Hotel Charges', 'amount' => 'Rs. 5000', 'status' => 'Paid'],
            ['description' => 'Convenience Fee', 'amount' => 'Rs. 250', 'status' => 'Paid'],
            ['description' => 'Total Amount', 'amount' => 'Rs. 5250', 'status' => 'Paid']
        ];
        
        $pdf->BookingInfo('Hotel Booking Details', $bookingInfo);
        $pdf->PaymentInfo($paymentInfo);
    }
    
} elseif (($type == 'flight' || $type == 'train') && !empty($ref)) {
    // Flight/Train booking PDF
    $bookingSQL = "SELECT * FROM booking_amounts WHERE booking_reference = ? AND username = ?";
    $stmt = $conn->prepare($bookingSQL);
    $stmt->bind_param("ss", $ref, $_SESSION["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    
    if ($booking) {
        $details = json_decode($booking['booking_details'], true);
        
        $bookingInfo = [
            'Booking Reference' => $booking['booking_reference'],
            'Booking Type' => ucfirst($booking['booking_type']),
            'Route' => $booking['origin'] . ' → ' . $booking['destination'],
            'Travel Date' => $booking['travel_date'],
            'Passengers' => $booking['passengers'],
            'Guest Name' => $booking['username'],
            'Status' => ucfirst($booking['status'])
        ];
        
        if ($type == 'flight') {
            $bookingInfo['Flight Type'] = isset($details['type']) ? $details['type'] : 'N/A';
            $bookingInfo['Class'] = isset($details['class']) ? $details['class'] : 'N/A';
            $bookingInfo['Operator'] = isset($details['operator']) ? $details['operator'] : 'N/A';
        } elseif ($type == 'train') {
            $bookingInfo['Train Name'] = isset($details['train_name']) ? $details['train_name'] : 'N/A';
            $bookingInfo['Class'] = isset($details['class']) ? $details['class'] : 'N/A';
        }
        
        $paymentInfo = [
            ['description' => 'Base Fare', 'amount' => 'Rs. ' . number_format($booking['base_amount'], 2), 'status' => 'Paid'],
            ['description' => 'Convenience Fee', 'amount' => 'Rs. ' . number_format($booking['convenience_fee'], 2), 'status' => 'Paid'],
            ['description' => 'Total Amount', 'amount' => 'Rs. ' . number_format($booking['total_amount'], 2), 'status' => 'Paid']
        ];
        
        $pdf->BookingInfo(ucfirst($type) . ' Booking Details', $bookingInfo);
        $pdf->PaymentInfo($paymentInfo);
    }
}

// Add important information
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Important Information', 0, 1, 'L');
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 9);
$importantInfo = [
    '1. This is a computer generated receipt and does not require signature.',
    '2. Please carry a valid government ID proof during travel.',
    '3. Cancellation charges may apply as per terms and conditions.',
    '4. For any queries, please contact customer support.',
    '5. Keep this receipt for your records.'
];

foreach($importantInfo as $info) {
    $pdf->Cell(0, 5, $info, 0, 1, 'L');
}

// Output PDF
$filename = $type . '_booking_' . ($id ? $id : $ref) . '.pdf';
$pdf->Output('D', $filename);

$conn->close();
?>