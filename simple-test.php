<?php
// Very simple test to check if PHP and AJAX are working
session_start();

if (isset($_POST['test'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'PHP and AJAX are working!',
        'timestamp' => date('Y-m-d H:i:s'),
        'post_data' => $_POST
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Simple AJAX Test</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f0f0f0;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
    }

    button {
        background: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background: #45a049;
    }

    .result {
        margin-top: 20px;
        padding: 10px;
        border-radius: 5px;
    }

    .success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Simple AJAX Test</h2>
        <p>Click the button to test if PHP and AJAX are working correctly:</p>

        <button onclick="testConnection()">Test Connection</button>

        <div id="result"></div>
    </div>

    <script>
    function testConnection() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = 'Testing connection...';

        const formData = new FormData();
        formData.append('test', '1');

        fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);

                try {
                    const data = JSON.parse(text);

                    if (data.success) {
                        resultDiv.innerHTML = `
                            <div class="result success">
                                <strong>✅ Success!</strong><br>
                                Message: ${data.message}<br>
                                Timestamp: ${data.timestamp}
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <div class="result error">
                                <strong>❌ Failed!</strong><br>
                                ${data.message || 'Unknown error'}
                            </div>
                        `;
                    }
                } catch (e) {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <strong>❌ JSON Parse Error!</strong><br>
                            ${e.message}<br>
                            Raw response: ${text.substring(0, 200)}...
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                resultDiv.innerHTML = `
                    <div class="result error">
                        <strong>❌ Connection Error!</strong><br>
                        ${error.message}
                    </div>
                `;
            });
    }
    </script>
</body>

</html>