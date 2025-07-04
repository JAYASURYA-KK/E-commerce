<?php
include("connection/connect.php");
session_start();

// Check if user is logged in using your existing system
if(empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user's orders with items
$orders_query = "SELECT o.*, 
                 GROUP_CONCAT(CONCAT(oi.product_name, ' (Qty: ', oi.quantity, ')') SEPARATOR ', ') as items
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                 WHERE o.user_id = ? AND o.payment_status = 'paid'
                 GROUP BY o.order_id 
                 ORDER BY o.order_date DESC";
$stmt = mysqli_prepare($db, $orders_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History</title>
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
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: var(--main-maroon);
        text-align: center;
        margin-bottom: 30px;
    }

    .nav-links {
        text-align: center;
        margin-bottom: 30px;
    }

    .nav-links a {
        color: var(--main-maroon);
        text-decoration: none;
        margin: 0 15px;
        padding: 10px 20px;
        border: 1px solid var(--main-maroon);
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-links a:hover {
        background-color: var(--main-maroon);
        color: white;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .orders-table th {
        background-color: var(--main-maroon);
        color: white;
        padding: 15px 12px;
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .orders-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    .orders-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .orders-table tbody tr:last-child td {
        border-bottom: none;
    }

    .order-id {
        font-weight: bold;
        color: var(--deep-maroon);
    }

    .order-date {
        color: #666;
        font-size: 13px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-processing {
        background-color: #d4edda;
        color: #155724;
    }

    .status-shipped {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-delivered {
        background-color: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .amount {
        font-weight: bold;
        color: var(--deep-maroon);
    }

    .customer-info {
        font-size: 13px;
        line-height: 1.4;
    }

    .address-info {
        font-size: 13px;
        line-height: 1.4;
        max-width: 200px;
    }

    .items-list {
        font-size: 13px;
        line-height: 1.4;
        max-width: 250px;
    }

    .admin-message {
        background-color: #e8f4fd;
        padding: 8px 10px;
        border-radius: 4px;
        font-size: 12px;
        color: #007bff;
        border-left: 3px solid #007bff;
    }

    .no-orders {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 50px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .no-orders a {
        color: var(--main-maroon);
        text-decoration: none;
        font-weight: bold;
    }

    .no-orders a:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .container {
            padding: 20px;
        }

        .orders-table th,
        .orders-table td {
            padding: 10px 8px;
        }

        .orders-table th {
            font-size: 11px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .nav-links a {
            margin: 5px;
            padding: 8px 15px;
            font-size: 14px;
        }

        .table-container {
            font-size: 12px;
        }

        .orders-table th,
        .orders-table td {
            padding: 8px 6px;
        }

        .status-badge {
            font-size: 10px;
            padding: 3px 8px;
        }
    }

    @media (max-width: 480px) {

        /* Stack table on very small screens */
        .orders-table,
        .orders-table thead,
        .orders-table tbody,
        .orders-table th,
        .orders-table td,
        .orders-table tr {
            display: block;
        }

        .orders-table thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .orders-table tr {
            border: 1px solid #ccc;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background-color: white;
        }

        .orders-table td {
            border: none;
            position: relative;
            padding-left: 50% !important;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .orders-table td:before {
            content: attr(data-label) ": ";
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            font-weight: bold;
            color: var(--deep-maroon);
        }
    }
    </style>


    <script src="js/logo-animation.js" type="text/javascript"></script>

</head>

<body>
    <script src="../google-translate-widget.js"></script>
    <div class="container">
        <h1>My Order History</h1>

        <div class="nav-links">
            <a href="../home.php">Home</a>
            <a href="cart.php">My Cart</a>
            <a href="logout.php">Logout</a>
        </div>

        <?php if (mysqli_num_rows($orders_result) > 0): ?>
        <div class="table-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Customer Info</th>
                        <th>Shipping Address</th>
                        <th>Items</th>
                        <th>Transaction ID</th>
                        <th>Location Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td data-label="Order ID">
                            <div class="order-id"><?php echo $order['order_id']; ?></div>
                        </td>
                        <td data-label="Date">
                            <div class="order-date"><?php echo date('M j, Y', strtotime($order['order_date'])); ?><br>
                                <?php echo date('g:i A', strtotime($order['order_date'])); ?></div>
                        </td>
                        <td data-label="Status">
                            <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                        <td data-label="Amount">
                            <div class="amount">$<?php echo number_format($order['total_amount'], 2); ?></div>
                        </td>
                        <td data-label="Customer Info">
                            <div class="customer-info">
                                <strong><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['email_address']); ?><br>
                                <?php echo htmlspecialchars($order['contact_number']); ?>
                            </div>
                        </td>
                        <td data-label="Shipping Address">
                            <div class="address-info">
                                <?php echo htmlspecialchars($order['house_number'] . ', ' . $order['street_road']); ?><br>
                                <?php echo htmlspecialchars($order['town_city'] . ', ' . $order['post_code']); ?><br>
                                <?php echo htmlspecialchars($order['country']); ?>
                            </div>
                        </td>
                        <td data-label="Items">
                            <div class="items-list">
                                <?php echo !empty($order['items']) ? htmlspecialchars($order['items']) : 'No items'; ?>
                            </div>
                        </td>
                        <td data-label="Transaction ID">
                            <?php echo htmlspecialchars($order['transaction_id']); ?>
                        </td>
                        <td data-label="Location Code">
                            <?php if (!empty($order['admin_command'])): ?>
                            <div class="admin-message">
                                <?php echo htmlspecialchars($order['admin_command']); ?>
                            </div>
                            <?php else: ?>
                            <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-orders">
            <p>You haven't placed any orders yet.</p>
            <a href="index.php">Start Shopping</a>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>