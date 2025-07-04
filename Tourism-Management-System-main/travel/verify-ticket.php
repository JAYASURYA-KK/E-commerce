<?php
// Ticket verification page that displays when QR code is scanned

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectmeteor";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters from QR code
$bookingId = isset($_GET['id']) ? $_GET['id'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$user = isset($_GET['user']) ? $_GET['user'] : '';

$ticket = null;
$error = '';

if($bookingId && $type && $user) {
    // Fetch ticket details
    if($type == 'flight') {
        $sql = "SELECT * FROM `flightbookings` WHERE bookingID='$bookingId' AND username='$user' AND cancelled='no'";
    } else {
        $sql = "SELECT * FROM `trainbookings` WHERE bookingID='$bookingId' AND username='$user' AND cancelled='no'";
    }
    
    $result = $conn->query($sql);
    if($result && $result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
    } else {
        $error = "Ticket not found or has been cancelled.";
    }
} else {
    $error = "Invalid QR code data.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification - JS Weby Tourism</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        padding: 30px 20px;
        text-align: center;
    }

    .header h1 {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .header p {
        opacity: 0.9;
        font-size: 14px;
    }

    .content {
        padding: 30px 20px;
    }

    .ticket-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }

    .info-value {
        font-weight: 700;
        color: #212529;
        font-size: 14px;
        text-align: right;
    }

    .status {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .status.valid {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status.invalid {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .status i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }

    .verification-code {
        background: #e9ecef;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        font-family: 'Courier New', monospace;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    .footer {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 12px;
    }

    .route {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .route .arrow {
        margin: 0 15px;
        color: #007bff;
    }

    @media (max-width: 480px) {
        .container {
            margin: 10px;
            border-radius: 10px;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .info-value {
            text-align: left;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fa fa-shield"></i> JS Weby Tourism</h1>
            <p>Ticket Verification System</p>
        </div>

        <div class="content">
            <?php if($ticket): ?>
            <div class="status valid">
                <i class="fa fa-check-circle"></i>
                <strong>✅ VALID TICKET</strong>
                <p>This ticket is authentic and valid for travel.</p>
            </div>

            <div class="route">
                <span><?php echo htmlspecialchars($ticket['origin']); ?></span>
                <span class="arrow">
                    <?php echo $type == 'flight' ? '✈️' : '🚂'; ?> →
                </span>
                <span><?php echo htmlspecialchars($ticket['destination']); ?></span>
            </div>

            <div class="ticket-info">
                <div class="info-row">
                    <span class="info-label">Booking ID</span>
                    <span class="info-value"><?php echo htmlspecialchars($ticket['bookingID']); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Passenger Name</span>
                    <span class="info-value"><?php echo strtoupper(htmlspecialchars($ticket['username'])); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Travel Date</span>
                    <span class="info-value"><?php echo date('d M Y', strtotime($ticket['date'])); ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Travel Type</span>
                    <span class="info-value"><?php echo strtoupper($type); ?></span>
                </div>

                <?php if($type == 'flight' && isset($ticket['type'])): ?>
                <div class="info-row">
                    <span class="info-label">Flight Type</span>
                    <span class="info-value">
                        <?php echo $ticket['type'] == 'OneWayFlight' ? 'One Way' : 'Return Trip'; ?>
                    </span>
                </div>
                <?php endif; ?>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="color: #28a745;">CONFIRMED</span>
                </div>
            </div>

            <div class="verification-code">
                <?php echo strtoupper(substr(md5($ticket['bookingID'] . $ticket['username']), 0, 8)); ?>
            </div>

            <?php else: ?>
            <div class="status invalid">
                <i class="fa fa-times-circle"></i>
                <strong>❌ INVALID TICKET</strong>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p><strong>JS Weby Tourism</strong></p>
            <p>For support: JSweby@gmail.com | Phone: +91 9080418085</p>
            <p>Scanned on: <?php echo date('d M Y H:i:s'); ?></p>
        </div>
    </div>
</body>

</html>