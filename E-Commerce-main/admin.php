<?php
include("connection/connect.php");
session_start();

// Check if user is logged in using your existing system
if(empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle status and command update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    $admin_command = trim($_POST['admin_command']);
    
    // Validate command (max 10 words)
    $word_count = str_word_count($admin_command);
    if ($word_count > 10) {
        $message = "Command must be 10 words or less!";
    } else {
        $update_query = "UPDATE orders SET order_status = ?, admin_command = ? WHERE order_id = ?";
        $stmt = mysqli_prepare($db, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $new_status, $admin_command, $order_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Order updated successfully!";
        } else {
            $message = "Error updating order.";
        }
    }
}

// Fetch all orders with user details
$orders_query = "SELECT o.*, u.username 
                FROM orders o 
                JOIN users u ON o.user_id = u.u_id 
                WHERE o.payment_status = 'paid'
                ORDER BY o.order_date DESC";
$orders_result = mysqli_query($db, $orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
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
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: var(--main-maroon);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .orders-grid {
            display: grid;
            gap: 20px;
        }
        
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafafa;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .order-id {
            font-size: 18px;
            font-weight: bold;
            color: var(--deep-maroon);
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d1ecf1; color: #0c5460; }
        .status-processing { background-color: #d4edda; color: #155724; }
        .status-shipped { background-color: #cce5ff; color: #004085; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: bold;
            color: var(--deep-maroon);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #333;
            font-size: 14px;
        }
        
        .admin-controls {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .control-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        select, input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .command-input {
            flex: 1;
            min-width: 200px;
        }
        
        .update-btn {
            background-color: var(--main-maroon);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .update-btn:hover {
            background-color: var(--deep-maroon);
        }
        
        .word-counter {
            font-size: 12px;
            color: #666;
            margin-left: 10px;
        }
        
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .nav-links a {
            color: var(--main-maroon);
            text-decoration: none;
            margin: 0 15px;
            padding: 10px 20px;
            border: 1px solid var(--main-maroon);
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            background-color: var(--main-maroon);
            color: white;
        }
        
        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .control-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .command-input {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin - Order Management</h1>
        
        <div class="nav-links">
            <a href="../home.html">Home</a>
            <a href="your_orders.php">My Orders</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="orders-grid">
            <?php if(mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                        <span class="status-badge status-<?php echo $order['order_status']; ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </div>
                    
                    <div class="order-details">
                        <div class="detail-item">
                            <span class="detail-label">Customer</span>
                            <span class="detail-value">
                                <strong><?php echo htmlspecialchars($order['username']); ?></strong><br>
                                <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                                <?php echo htmlspecialchars($order['email_address']); ?><br>
                                <?php echo htmlspecialchars($order['contact_number']); ?>
                            </span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Order Info</span>
                            <span class="detail-value">
                                Amount: $<?php echo number_format($order['total_amount'], 2); ?><br>
                                Transaction: <?php echo htmlspecialchars($order['transaction_id']); ?><br>
                                Date: <?php echo date('M j, Y', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Shipping Address</span>
                            <span class="detail-value">
                                <?php echo htmlspecialchars($order['house_number'] . ', ' . $order['street_road']); ?><br>
                                <?php echo htmlspecialchars($order['town_city'] . ', ' . $order['post_code']); ?><br>
                                <?php echo htmlspecialchars($order['country']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['admin_command'])): ?>
                    <div class="detail-item" style="margin-bottom: 15px;">
                        <span class="detail-label">Current Admin Command</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['admin_command']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="admin-controls">
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            
                            <div class="control-row">
                                <select name="order_status" required>
                                    <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $order['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                
                                <input type="text" name="admin_command" placeholder="Type command (max 10 words)" 
                                       class="command-input" value="<?php echo htmlspecialchars($order['admin_command']); ?>"
                                       onkeyup="countWords(this)" maxlength="100">
                                
                                <span class="word-counter" id="counter-<?php echo $order['order_id']; ?>">0/10 words</span>
                                
                                <button type="submit" name="update_order" class="update-btn">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 50px; color: #666;">
                    <p>No paid orders found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function countWords(input) {
            const words = input.value.trim().split(/\s+/).filter(word => word.length > 0);
            const wordCount = words.length;
            const orderId = input.closest('form').querySelector('input[name="order_id"]').value;
            const counter = document.getElementById('counter-' + orderId);
            
            counter.textContent = wordCount + '/10 words';
            
            if (wordCount > 10) {
                counter.style.color = 'red';
                input.style.borderColor = 'red';
            } else {
                counter.style.color = '#666';
                input.style.borderColor = '#ddd';
            }
        }
        
        // Initialize word counters on page load
        document.addEventListener('DOMContentLoaded', function() {
            const commandInputs = document.querySelectorAll('.command-input');
            commandInputs.forEach(input => {
                countWords(input);
            });
        });
    </script>
</body>
</html>