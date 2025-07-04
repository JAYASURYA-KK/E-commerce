<?php
include("connection/connect.php");
session_start();

// Check if user is logged in using your existing system
if(empty($_SESSION["user_id"]) || !isset($_SESSION['pending_order_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$order_id = $_SESSION['pending_order_id'];
$message = "";
$payment_success = false;

// Handle payment verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_payment'])) {
    $transaction_id = trim($_POST['transaction_id']);
    
    if (!empty($transaction_id)) {
        // For demo, accept transaction ID "12345678" or any 8-digit number
        if ($transaction_id === "12345678" || (strlen($transaction_id) == 8 && is_numeric($transaction_id))) {
            // Update order with transaction ID and mark as paid
            $update_query = "UPDATE orders SET transaction_id = ?, payment_status = 'paid', 
                           order_status = 'confirmed' WHERE order_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($db, $update_query);
            mysqli_stmt_bind_param($stmt, "sii", $transaction_id, $order_id, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Clear cart and pending order
                unset($_SESSION['mycart']);
                unset($_SESSION['pending_order_id']);
                
                $payment_success = true;
                $message = "✅ Payment verified successfully! Your order has been confirmed.";
            } else {
                $message = "❌ Error processing payment. Please try again.";
            }
        } else {
            $message = "❌ Invalid transaction ID. Please enter a valid 8-digit transaction ID.";
        }
    } else {
        $message = "⚠️ Please enter your transaction ID.";
    }
}

// Get order details
$order_query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = mysqli_prepare($db, $order_query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($order_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment | Complete Your Order</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        
        :root {
            --main-maroon: #CE5959;
            --deep-maroon: #89375F;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        
        .payment-container {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-head {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--main-maroon);
        }
        
        .payment-line {
            width: 160px;
            height: 3px;
            border-radius: 10px;
            background-color: var(--main-maroon);
            display: inline-block;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px dashed var(--main-maroon);
            border-radius: 12px;
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }
        
        .qr-image {
            width: 250px;
            height: 250px;
            background-color: #ddd;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        
        .transaction-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        
        .transaction-input {
            width: 100%;
            max-width: 400px;
            height: 50px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .verify-btn {
            width: 200px;
            height: 50px;
            background-color: var(--main-maroon);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .verify-btn:hover {
            background-color: var(--deep-maroon);
        }
        
        .payment-message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }
        
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .goback-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px;
        }
        
        .goback-btn:hover {
            background-color: #5a6268;
        }
        
        .demo-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <p class="payment-head">Complete Your Payment</p>
            <span class="payment-line"></span>
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
        </div>
        
        <?php if ($payment_success): ?>
            <div class="payment-message success-message">
                <?php echo $message; ?>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <p>Order ID: <strong>#<?php echo $order_id; ?></strong></p>
                <p>Thank you for your purchase!</p>
                <a href="your_orders.php" class="goback-btn">View My Orders</a>
                <a href="../home.html" class="goback-btn">Continue Shopping</a>
            </div>
            
        <?php else: ?>
            <div class="demo-info">
                <strong>Demo Payment:</strong> Use transaction ID "12345678" for successful payment
            </div>
            
            <div class="qr-container">
                <div class="qr-image">
                    [Google Pay QR Code]<br><br>
                    <strong>Amount: $<?php echo number_format($order['total_amount'], 2); ?></strong><br>
                    Scan with Google Pay App
                </div>
                <p><strong>Pay using Google Pay</strong></p>
                <p>UPI ID: <strong>merchant@gpay</strong></p>
            </div>
            
            <form method="POST" class="transaction-form">
                <input type="text" name="transaction_id" placeholder="Enter 8-digit Transaction ID" 
                       class="transaction-input" maxlength="8" required>
                <button type="submit" name="verify_payment" class="verify-btn">Verify Payment</button>
            </form>
            
            <?php if (!empty($message)): ?>
                <div class="payment-message error-message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="checkout.php" class="goback-btn">Back to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>