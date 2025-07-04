<?php 
    include_once('./includes/headerNav.php');
?>
<?php
include("connect.php");
session_start();

// Check if admin is logged in
if(empty($_SESSION["admin_id"]) && empty($_SESSION["user_id"])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status update
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $location_code = $_POST['location_code'];
    $location_type = $_POST['location_type'];
    
    // Check if location columns exist, if not add them
    $check_columns = "SHOW COLUMNS FROM orders LIKE 'location_code'";
    $result = mysqli_query($db, $check_columns);
    
    if(mysqli_num_rows($result) == 0) {
        // Add missing columns
        $alter_query = "ALTER TABLE orders 
                       ADD COLUMN location_code VARCHAR(100) DEFAULT NULL,
                       ADD COLUMN location_type VARCHAR(50) DEFAULT NULL";
        mysqli_query($db, $alter_query);
    }
    
    $update_query = "UPDATE orders SET order_status = ?, admin_command = ?, location_code = ?, location_type = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($db, $update_query);
    $admin_message = "Status: $new_status | Location: $location_type - $location_code";
    mysqli_stmt_bind_param($stmt, "ssssi", $new_status, $admin_message, $location_code, $location_type, $order_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $success_message = "Order updated successfully!";
    } else {
        $error_message = "Error updating order: " . mysqli_error($db);
    }
}

// FIXED QUERY: Using correct table name 'order_items' instead of 'users_orders'
// and correct column names based on your database structure
$orders_query = "SELECT o.*, 
                 GROUP_CONCAT(CONCAT(oi.product_name, ' (Qty: ', oi.quantity, ', Price: $', oi.product_price, ')') SEPARATOR '<br>') as items,
                 COUNT(oi.item_id) as item_count,
                 SUM(oi.subtotal) as calculated_total
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                 WHERE o.payment_status = 'paid'
                 GROUP BY o.order_id 
                 ORDER BY o.order_date DESC";

$orders_result = mysqli_query($db, $orders_query);

if (!$orders_result) {
    die("Query failed: " . mysqli_error($db) . "<br>Query: " . $orders_query);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management | OnlineFoodPHP</title>
    <style>
    * {
        font-family: Arial, Helvetica, sans-serif;
        box-sizing: border-box;
    }

    :root {
        --main-maroon: #CE5959;
        --deep-maroon: #89375F;
        --success-green: #28a745;
        --warning-orange: #ffc107;
        --danger-red: #dc3545;
        --food-orange: #ff6b35;
        --food-red: #f7931e;
    }

    body {
        margin: 0;
        padding: 20px;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        min-height: 100vh;
    }

    .container {
        max-width: 1800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .admin-header {
        background: linear-gradient(135deg, var(--food-orange), var(--food-red));
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    }

    .admin-header h1 {
        margin: 0;
        font-size: 2.2em;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .admin-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 1.1em;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: bold;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left-color: #28a745;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border-left-color: #dc3545;
    }

    .stats-bar {
        display: flex;
        justify-content: space-around;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: var(--food-orange);
    }

    .stat-label {
        color: #666;
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
    }

    .orders-table th {
        background: linear-gradient(135deg, var(--food-orange), var(--food-red));
        color: white;
        padding: 18px 15px;
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .orders-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    .orders-table tbody tr:hover {
        background-color: #fff8f5;
        transform: scale(1.001);
        transition: all 0.2s ease;
    }

    .orders-table tbody tr:last-child td {
        border-bottom: none;
    }

    .order-id {
        font-weight: bold;
        color: var(--food-orange);
        font-size: 1.1em;
    }

    .order-date {
        color: #666;
        font-size: 13px;
    }

    .status-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        display: inline-block;
        text-align: center;
        min-width: 80px;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-confirmed {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .status-processing {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #99d6ff;
    }

    .status-shipped {
        background-color: #e2e3ff;
        color: #383d41;
        border: 1px solid #c7c8ff;
    }

    .status-delivered {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .amount {
        font-weight: bold;
        color: var(--food-red);
        font-size: 1.2em;
    }

    .customer-info {
        font-size: 13px;
        line-height: 1.5;
    }

    .address-info {
        font-size: 13px;
        line-height: 1.5;
        max-width: 200px;
    }

    .items-list {
        font-size: 12px;
        line-height: 1.6;
        max-width: 300px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        border-left: 3px solid var(--food-orange);
    }

    .admin-controls {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        min-width: 220px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .admin-controls select,
    .admin-controls input {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 13px;
        transition: border-color 0.3s ease;
    }

    .admin-controls select:focus,
    .admin-controls input:focus {
        border-color: var(--food-orange);
        outline: none;
        box-shadow: 0 0 5px rgba(255, 107, 53, 0.3);
    }

    .update-btn {
        background: linear-gradient(135deg, var(--success-green), #20c997);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
        font-weight: bold;
        margin-top: 8px;
        width: 100%;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .update-btn:hover {
        background: linear-gradient(135deg, #218838, #1e7e34);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(40, 167, 69, 0.3);
    }

    .location-info {
        font-size: 11px;
        background-color: #e3f2fd;
        padding: 8px;
        border-radius: 5px;
        margin-top: 8px;
        border-left: 3px solid #2196f3;
    }

    .no-orders {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 60px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        border: 2px dashed #dee2e6;
    }

    .no-orders h3 {
        color: var(--food-orange);
        margin-bottom: 15px;
    }

    .debug-info {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-family: monospace;
        font-size: 12px;
    }

    /* Responsive Design */
    @media (max-width: 1400px) {
        .container {
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .stats-bar {
            flex-direction: column;
            gap: 15px;
        }

        .orders-table th,
        .orders-table td {
            padding: 10px 8px;
        }

        .admin-controls {
            min-width: 180px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="admin-header">
            <h1>🍽️ Order Management System</h1>
            <p>Complete Order Management System with Location Tracking</p>
        </div>

        <?php if(isset($success_message)): ?>
        <div class="alert alert-success">
            ✅ <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($error_message)): ?>
        <div class="alert alert-error">
            ❌ <?php echo $error_message; ?>
        </div>
        <?php endif; ?>


        <?php
        // Calculate stats
        $total_orders = mysqli_num_rows($orders_result);
        mysqli_data_seek($orders_result, 0); // Reset result pointer
        
        $total_revenue = 0;
        $status_counts = [];
        while($row = mysqli_fetch_assoc($orders_result)) {
            // Use calculated_total if available, otherwise fall back to total_amount
            $order_total = !empty($row['calculated_total']) ? $row['calculated_total'] : $row['total_amount'];
            $total_revenue += $order_total;
            $status = $row['order_status'];
            $status_counts[$status] = ($status_counts[$status] ?? 0) + 1;
        }
        mysqli_data_seek($orders_result, 0); // Reset again for main loop
        ?>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_counts['delivered'] ?? 0; ?></div>
                <div class="stat-label">Delivered</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $status_counts['pending'] ?? 0; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <?php if (mysqli_num_rows($orders_result) > 0): ?>
        <div class="table-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order Details</th>
                        <th>Customer Info</th>
                        <th>Status & Amount</th>
                        <th>Delivery Address</th>
                        <th>Items</th>
                        <th>Payment Info</th>
                        <th>Admin Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td>
                            <div class="order-id">#<?php echo $order['order_id']; ?></div>
                            <div class="order-date">
                                📅 <?php echo date('M j, Y', strtotime($order['order_date'])); ?><br>
                                🕐 <?php echo date('g:i A', strtotime($order['order_date'])); ?>
                            </div>
                            <?php if($order['updated_at']): ?>
                            <small style="color: #999;">Updated:
                                <?php echo date('M j, g:i A', strtotime($order['updated_at'])); ?></small>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="customer-info">
                                <strong>👤
                                    <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                                📧 <?php echo htmlspecialchars($order['email_address']); ?><br>
                                📱 <?php echo htmlspecialchars($order['contact_number']); ?><br>
                                <small>User ID: <?php echo $order['user_id']; ?></small>
                            </div>
                        </td>

                        <td>
                            <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                            <div class="amount">
                                $<?php echo number_format(!empty($order['calculated_total']) ? $order['calculated_total'] : $order['total_amount'], 2); ?>
                            </div>
                            <small><?php echo $order['item_count']; ?> items</small>
                            <?php if(!empty($order['calculated_total']) && $order['calculated_total'] != $order['total_amount']): ?>
                            <br><small style="color: #dc3545;">⚠️ Total mismatch detected</small>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="address-info">
                                🏠
                                <?php echo htmlspecialchars($order['house_number'] . ', ' . $order['street_road']); ?><br>
                                🏙️
                                <?php echo htmlspecialchars($order['town_city'] . ', ' . $order['post_code']); ?><br>
                                🌍 <?php echo htmlspecialchars($order['country']); ?>
                            </div>
                        </td>

                        <td>
                            <div class="items-list">
                                <?php echo !empty($order['items']) ? $order['items'] : '<em>No items found - Check database structure</em>'; ?>
                            </div>
                        </td>

                        <td>
                            <strong>💳 <?php echo ucfirst($order['payment_status']); ?></strong><br>
                            <small>TXN: <?php echo htmlspecialchars($order['transaction_id']); ?></small>
                        </td>

                        <td>
                            <form method="POST" class="admin-controls">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">

                                <label><strong>📋 Order Status:</strong></label>
                                <select name="new_status" required>
                                    <option value="pending"
                                        <?php echo ($order['order_status'] == 'pending') ? 'selected' : ''; ?>>🔄
                                        Pending</option>
                                    <option value="confirmed"
                                        <?php echo ($order['order_status'] == 'confirmed') ? 'selected' : ''; ?>>✅
                                        Confirmed</option>
                                    <option value="processing"
                                        <?php echo ($order['order_status'] == 'processing') ? 'selected' : ''; ?>>👨‍🍳
                                        Processing</option>
                                    <option value="shipped"
                                        <?php echo ($order['order_status'] == 'shipped') ? 'selected' : ''; ?>>🚚
                                        Shipped</option>
                                    <option value="delivered"
                                        <?php echo ($order['order_status'] == 'delivered') ? 'selected' : ''; ?>>🎉
                                        Delivered</option>
                                    <option value="cancelled"
                                        <?php echo ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>❌
                                        Cancelled</option>
                                </select>

                                <label><strong>📍 Location Type:</strong></label>
                                <select name="location_type">
                                    <option value="">Select Location</option>
                                    <option value="kitchen"
                                        <?php echo (($order['location_type'] ?? '') == 'kitchen') ? 'selected' : ''; ?>>
                                        🏠 Near</option>
                                    <option value="packaging"
                                        <?php echo (($order['location_type'] ?? '') == 'packaging') ? 'selected' : ''; ?>>
                                        📦 Packaging</option>
                                    <option value="ready_pickup"
                                        <?php echo (($order['location_type'] ?? '') == 'ready_pickup') ? 'selected' : ''; ?>>
                                        ✅ Ready for Pickup</option>
                                    <option value="out_delivery"
                                        <?php echo (($order['location_type'] ?? '') == 'out_delivery') ? 'selected' : ''; ?>>
                                        🚗 Out for Delivery</option>
                                    <option value="delivered"
                                        <?php echo (($order['location_type'] ?? '') == 'delivered') ? 'selected' : ''; ?>>
                                        🏠 Delivered</option>
                                </select>

                                <label><strong>🏷️ Location Code:</strong></label>
                                <input type="text" name="location_code" placeholder="e.g., KIT-001, DEL-A23"
                                    value="<?php echo htmlspecialchars($order['location_code'] ?? ''); ?>">

                                <button type="submit" name="update_status" class="update-btn">
                                    🔄 Update Order
                                </button>

                                <?php if(!empty($order['admin_command'])): ?>
                                <div class="location-info">
                                    <strong>📝 Admin Notes:</strong><br>
                                    <?php echo htmlspecialchars($order['admin_command']); ?>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($order['location_code']) || !empty($order['location_type'])): ?>
                                <div class="location-info">
                                    <strong>📍 Current Location:</strong><br>
                                    Type: <?php echo htmlspecialchars($order['location_type'] ?? 'N/A'); ?><br>
                                    Code: <?php echo htmlspecialchars($order['location_code'] ?? 'N/A'); ?>
                                </div>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-orders">
            <h3>🍽️ No Orders Found</h3>
            <p>No paid orders are currently in the system.</p>
            <p>Orders will appear here once customers place and pay for their food orders.</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
    // Confirmation before updating order
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const status = form.querySelector('select[name="new_status"]').value;
            const locationCode = form.querySelector('input[name="location_code"]').value;

            if (!confirm(
                    `Are you sure you want to update this order to "${status}"${locationCode ? ' with location code "' + locationCode + '"' : ''}?`
                )) {
                e.preventDefault();
            }
        });
    });

    // Auto-refresh every 2 minutes for real-time updates
    setTimeout(function() {
        location.reload();
    }, 120000);
    </script>
</body>

</html>