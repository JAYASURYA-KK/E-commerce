<?php
// Test script to verify QR code generation works

function testQRGeneration() {
    echo "<h2>Testing QR Code Generation</h2>";
    
    $testData = "TICKET:TEST123|TYPE:FLIGHT|FROM:Delhi|TO:Mumbai|DATE:2024-01-15|PASSENGER:testuser";
    
    // Test QR Server API
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($testData);
    
    echo "<h3>QR Server API Test:</h3>";
    echo "<p>URL: <a href='$qrUrl' target='_blank'>$qrUrl</a></p>";
    echo "<img src='$qrUrl' alt='Test QR Code' style='border: 1px solid #ccc; padding: 10px;'>";
    
    // Test if we can fetch the image
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $imageData = @file_get_contents($qrUrl, false, $context);
    
    if($imageData !== false) {
        echo "<p style='color: green;'>✅ QR Code generation is working!</p>";
        echo "<p>Image size: " . strlen($imageData) . " bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ QR Code generation failed!</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
    
    // Alternative APIs to test
    echo "<h3>Alternative QR Code APIs:</h3>";
    
    $alternatives = [
        "QR Code Generator" => "https://qr-code-generator.com/api/qr-code/?data=" . urlencode($testData) . "&size=150",
        "QuickChart" => "https://quickchart.io/qr?text=" . urlencode($testData) . "&size=150",
    ];
    
    foreach($alternatives as $name => $url) {
        echo "<h4>$name:</h4>";
        echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";
        echo "<img src='$url' alt='$name QR Code' style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
    }
}

// Run the test
testQRGeneration();
?>