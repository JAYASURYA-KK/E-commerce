<?php
session_start();
error_reporting(0);

// Database connection
$db_food = new mysqli("localhost", "root", "", "onlinefoodphp");

if ($db_food->connect_error) {
    die("Connection failed: " . $db_food->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$success = "";

// Get user balance
$balance_query = $db_food->prepare("SELECT * FROM user_balances WHERE user_id = ?");
$balance_query->bind_param("i", $user_id);
$balance_query->execute();
$balance_result = $balance_query->get_result();
$user_balance = $balance_result->fetch_assoc();

if (!$user_balance) {
    $message = "No balance found. Please spin the wheel first.";
    $can_withdraw = false;
    $total_amount = 0;
    $consecutive_days = 0;
} else {
    $total_amount = $user_balance['total_amount'];
    $consecutive_days = $user_balance['consecutive_days'];
    $can_withdraw = $user_balance['can_withdraw'];
}

// Create withdrawals table if not exists
$create_withdrawals_table = "CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    bank_details TEXT,
    withdrawal_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_date TIMESTAMP NULL,
    admin_notes TEXT
)";
$db_food->query($create_withdrawals_table);

// Handle withdrawal request
if (isset($_POST['withdraw']) && $can_withdraw && $total_amount >= 70) {
    $bank_name = mysqli_real_escape_string($db_food, $_POST['bank_name']);
    $account_number = mysqli_real_escape_string($db_food, $_POST['account_number']);
    $account_holder = mysqli_real_escape_string($db_food, $_POST['account_holder']);
    $ifsc_code = mysqli_real_escape_string($db_food, $_POST['ifsc_code']);
    
    $bank_details = json_encode([
        'bank_name' => $bank_name,
        'account_number' => $account_number,
        'account_holder' => $account_holder,
        'ifsc_code' => $ifsc_code
    ]);
    
    // Insert withdrawal request
    $insert_withdrawal = $db_food->prepare("INSERT INTO withdrawals (user_id, amount, bank_details) VALUES (?, ?, ?)");
    $insert_withdrawal->bind_param("ids", $user_id, $total_amount, $bank_details);
    
    if ($insert_withdrawal->execute()) {
        // Reset user balance and consecutive days
        $reset_balance = $db_food->prepare("UPDATE user_balances SET total_amount = 0, consecutive_days = 0, can_withdraw = FALSE WHERE user_id = ?");
        $reset_balance->bind_param("i", $user_id);
        $reset_balance->execute();
        
        $success = "Withdrawal request submitted successfully! You will receive your money within 3-5 business days.";
        $total_amount = 0;
        $consecutive_days = 0;
        $can_withdraw = false;
    } else {
        $message = "Error submitting withdrawal request. Please try again.";
    }
}

// Get withdrawal history
$history_query = $db_food->prepare("SELECT * FROM withdrawals WHERE user_id = ? ORDER BY withdrawal_date DESC LIMIT 10");
$history_query->bind_param("i", $user_id);
$history_query->execute();
$withdrawal_history = $history_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Winnings - Spin Wheel</title>
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
        color: white;
        padding: 2rem;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .title {
        font-size: 2.5rem;
        background: linear-gradient(45deg, #FFD700, #FFA500);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }

    .balance-card {
        background: rgba(255, 255, 255, 0.2);
        padding: 1.5rem;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 2rem;
        border: 2px solid #FFD700;
    }

    .balance-amount {
        font-size: 3rem;
        font-weight: bold;
        color: #FFD700;
        margin-bottom: 0.5rem;
    }

    .balance-label {
        font-size: 1.2rem;
        opacity: 0.8;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        padding: 1rem;
        border-radius: 10px;
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: #FFD700;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-top: 0.5rem;
    }

    .form-section {
        background: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }

    .form-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #FFD700;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 1rem;
    }

    .form-group input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-group input:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    .withdraw-button {
        background: linear-gradient(45deg, #51cf66, #40c057);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 25px;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .withdraw-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(81, 207, 102, 0.4);
    }

    .withdraw-button:disabled {
        background: #95a5a6;
        cursor: not-allowed;
        transform: none;
    }

    .back-button {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .alert {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .alert-success {
        background: rgba(81, 207, 102, 0.3);
        border: 1px solid #51cf66;
        color: #51cf66;
    }

    .alert-error {
        background: rgba(255, 107, 107, 0.3);
        border: 1px solid #FF6B6B;
        color: #FF6B6B;
    }

    .requirements {
        background: rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .requirements h3 {
        color: #FFD700;
        margin-bottom: 1rem;
    }

    .requirements ul {
        list-style: none;
        padding: 0;
    }

    .requirements li {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .history-section {
        background: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 15px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .history-table th,
    .history-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .history-table th {
        background: rgba(255, 255, 255, 0.1);
        font-weight: bold;
        color: #FFD700;
    }

    .status-pending {
        color: #FFA500;
        font-weight: bold;
    }

    .status-approved {
        color: #51cf66;
        font-weight: bold;
    }

    .status-rejected {
        color: #FF6B6B;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        body {
            padding: 1rem;
        }

        .container {
            padding: 1rem;
        }

        .title {
            font-size: 2rem;
        }

        .balance-amount {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <a href="spin-wheel.php" class="back-button">← Back to Spin Wheel</a>

        <div class="header">
            <h1 class="title">💸 Withdraw Winnings</h1>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <?php if ($message): ?>
        <div class="alert alert-error">
            ❌ <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="balance-card">
            <div class="balance-amount">$<?php echo number_format($total_amount, 2); ?></div>
            <div class="balance-label">Available for Withdrawal</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $consecutive_days; ?></div>
                <div class="stat-label">Consecutive Days</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $can_withdraw ? "✅" : "❌"; ?></div>
                <div class="stat-label">Withdrawal Eligible</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo max(0, 10 - $consecutive_days); ?></div>
                <div class="stat-label">Days Remaining</div>
            </div>
        </div>

        <div class="requirements">
            <h3>📋 Withdrawal Requirements</h3>
            <ul>
                <li>✅ Minimum Balance: $70.00</li>
                <li>✅ Consecutive Days: 10 days</li>
                <li>✅ Daily Purchase: Required each day</li>
                <li>⏱️ Processing Time: 3-5 business days</li>
                <li>🏦 Bank Transfer: Direct to your account</li>
            </ul>
        </div>

        <?php if ($can_withdraw && $total_amount >= 70): ?>
        <div class="form-section">
            <h2 class="form-title">🏦 Bank Details</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" placeholder="Enter your bank name" required>
                </div>

                <div class="form-group">
                    <label for="account_holder">Account Holder Name</label>
                    <input type="text" id="account_holder" name="account_holder" placeholder="Enter account holder name"
                        required>
                </div>

                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" id="account_number" name="account_number" placeholder="Enter account number"
                        required>
                </div>

                <div class="form-group">
                    <label for="ifsc_code">IFSC Code</label>
                    <input type="text" id="ifsc_code" name="ifsc_code" placeholder="Enter IFSC code" required>
                </div>

                <button type="submit" name="withdraw" class="withdraw-button">
                    💸 Withdraw $<?php echo number_format($total_amount, 2); ?>
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="form-section">
            <h2 class="form-title">❌ Withdrawal Not Available</h2>
            <p>You need to meet the following requirements:</p>
            <ul style="margin-top: 1rem; padding-left: 2rem;">
                <?php if ($total_amount < 70): ?>
                <li>❌ Minimum balance of $70 (Current: $<?php echo number_format($total_amount, 2); ?>)</li>
                <?php endif; ?>
                <?php if ($consecutive_days < 10): ?>
                <li>❌ 10 consecutive days of spinning (Current: <?php echo $consecutive_days; ?> days)</li>
                <?php endif; ?>
            </ul>
            <p style="margin-top: 1rem;">Keep spinning daily and making purchases to unlock withdrawal!</p>
        </div>
        <?php endif; ?>

        <div class="history-section">
            <h2 class="form-title">📊 Withdrawal History</h2>
            <?php if ($withdrawal_history->num_rows > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Processed Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $withdrawal_history->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['withdrawal_date'])); ?></td>
                        <td>$<?php echo number_format($row['amount'], 2); ?></td>
                        <td class="status-<?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                        <td>
                            <?php echo $row['processed_date'] ? date('M d, Y', strtotime($row['processed_date'])) : '-'; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No withdrawal history found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>