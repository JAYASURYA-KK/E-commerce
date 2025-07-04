<?php
/*
QR Code Generation Methods for PHP

Method 1: Google Charts API (Used in main file)
- Free and easy to use
- No installation required
- Requires internet connection

Method 2: PHP QR Code Library (Alternative)
- Download from: http://phpqrcode.sourceforge.net/
- Offline generation
- More customization options

Method 3: QR Server API (Alternative)
- Free API service
- Simple implementation
- Good for basic needs
*/

// Example usage of different QR code generation methods

// Method 1: Google Charts API (Current implementation)
function generateQRCodeGoogle($text, $size = 150) {
    return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($text);
}

// Method 2: QR Server API (Alternative)
function generateQRCodeServer($text, $size = 150) {
    return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($text);
}

// Method 3: Using PHP QR Code library (if installed)
function generateQRCodeLocal($text, $filename) {
    // Requires: include('phpqrcode/qrlib.php');
    // QRcode::png($text, $filename, QR_ECLEVEL_L, 4);
    // return $filename;
}

// Test the functions
$testData = "TICKET:FL001|TYPE:FLIGHT|FROM:Delhi|TO:Mumbai|DATE:2024-01-15|PASSENGER:john_doe";

echo "QR Code URLs:\n";
echo "Google Charts: " . generateQRCodeGoogle($testData) . "\n";
echo "QR Server: " . generateQRCodeServer($testData) . "\n";
?>