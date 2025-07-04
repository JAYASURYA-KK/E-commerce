<?php
// Test page to simulate QR code scanning

echo "<h2>QR Code Scan Test</h2>";

// Sample QR code URLs that would be generated
$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

$testUrls = [
    "Flight Ticket" => $baseUrl . "/verify-ticket.php?id=FL001&type=flight&user=john_doe",
    "Train Ticket" => $baseUrl . "/verify-ticket.php?id=TR001&type=train&user=jane_smith",
];

echo "<p>These are the URLs that would be embedded in QR codes:</p>";

foreach($testUrls as $name => $url) {
    echo "<h3>$name:</h3>";
    echo "<p><strong>QR Code URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
    echo "<p><strong>QR Code Image:</strong></p>";
    
    $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($url);
    echo "<img src='$qrImageUrl' alt='$name QR Code' style='border: 1px solid #ccc; margin: 10px;'>";
    echo "<hr>";
}

echo "<h3>How to Test:</h3>";
echo "<ol>";
echo "<li>Use your mobile phone's camera or QR scanner app</li>";
echo "<li>Scan one of the QR codes above</li>";
echo "<li>Your phone should open the verification page automatically</li>";
echo "<li>You'll see the ticket details in a mobile-friendly format</li>";
echo "</ol>";

echo "<h3>What the QR Code Contains:</h3>";
echo "<p>Instead of raw ticket data, the QR code now contains a web URL that:</p>";
echo "<ul>";
echo "<li>✅ Opens directly in any web browser</li>";
echo "<li>✅ Shows formatted ticket information</li>";
echo "<li>✅ Works on all mobile devices</li>";
echo "<li>✅ Provides verification status</li>";
echo "<li>✅ Includes security features</li>";
echo "</ul>";
?>