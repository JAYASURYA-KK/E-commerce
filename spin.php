<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple database connection (update these values)
$db_host = "localhost";
$db_user = "root"; // Change this to your database username
$db_pass = ""; // Change this to your database password
$db_name = "onlinefoodphp"; // Your database name

// Set test user if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 11; // Using user ID from your screenshots
    $_SESSION['username'] = 'test_user';
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$message = "";
$can_spin = false;
$total_amount = 0;
$consecutive_days = 0;

// Handle AJAX requests first
if (isset($_POST['action']) && $_POST['action'] === 'spin') {
    header('Content-Type: application/json');
    
    try {
        // Simple database connection
        $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($db->connect_error) {
            throw new Exception("Database connection failed: " . $db->connect_error);
        }
        
        // Create tables if they don't exist
        $create_spin_table = "CREATE TABLE IF NOT EXISTS spin_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            spin_date DATE NOT NULL,
            amount_won DECIMAL(10,2) NOT NULL,
            prize_text VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_date (user_id, spin_date)
        )";
        
        $create_balance_table = "CREATE TABLE IF NOT EXISTS user_balances (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            total_amount DECIMAL(10,2) DEFAULT 0,
            consecutive_days INT DEFAULT 0,
            last_spin_date DATE,
            can_withdraw BOOLEAN DEFAULT FALSE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->query($create_spin_table);
        $db->query($create_balance_table);
        
        // Check if already spun today
        $check_spin = $db->prepare("SELECT * FROM spin_history WHERE user_id = ? AND spin_date = ?");
        $check_spin->bind_param("is", $user_id, $today);
        $check_spin->execute();
        $already_spun = $check_spin->get_result()->num_rows > 0;
        
        if ($already_spun) {
            throw new Exception("You have already spun today!");
        }
        
        // Define prizes with their segment positions (0-7, starting from top)
        $prizes = [
            0 => ['amount' => 10, 'text' => 'Cash Prize'],     // Red segment
            1 => ['amount' => 8, 'text' => 'Voucher'],         // Teal segment  
            2 => ['amount' => 12, 'text' => 'Bonus'],          // Blue segment
            3 => ['amount' => 5, 'text' => 'Credit'],          // Green segment
            4 => ['amount' => 15, 'text' => 'Jackpot'],        // Yellow segment
            5 => ['amount' => 6, 'text' => 'Prize'],           // Purple segment
            6 => ['amount' => 20, 'text' => 'MEGA'],           // Light green segment
            7 => ['amount' => 0, 'text' => 'Try Again']        // Light yellow segment
        ];
        
        // Random prize selection
        $selected_segment = array_rand($prizes);
        $selected_prize = $prizes[$selected_segment];
        $won_amount = $selected_prize['amount'];
        $prize_text = $selected_prize['text'];
        
        // Record the spin
        $record_spin = $db->prepare("INSERT INTO spin_history (user_id, spin_date, amount_won, prize_text) VALUES (?, ?, ?, ?)");
        $record_spin->bind_param("isds", $user_id, $today, $won_amount, $prize_text);
        
        if (!$record_spin->execute()) {
            throw new Exception("Failed to record spin: " . $db->error);
        }
        
        // Get current balance
        $balance_query = $db->prepare("SELECT * FROM user_balances WHERE user_id = ?");
        $balance_query->bind_param("i", $user_id);
        $balance_query->execute();
        $balance_result = $balance_query->get_result();
        $user_balance = $balance_result->fetch_assoc();
        
        if (!$user_balance) {
            // Create new balance record
            $insert_balance = $db->prepare("INSERT INTO user_balances (user_id, total_amount, consecutive_days, last_spin_date) VALUES (?, ?, 1, ?)");
            $insert_balance->bind_param("ids", $user_id, $won_amount, $today);
            $insert_balance->execute();
            
            $new_total = $won_amount;
            $consecutive_days = 1;
        } else {
            // Update existing balance
            $current_total = $user_balance['total_amount'];
            $current_consecutive = $user_balance['consecutive_days'];
            $last_spin = $user_balance['last_spin_date'];
            
            $new_total = $current_total + $won_amount;
            
            // Check consecutive days
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            if ($last_spin == $yesterday) {
                $consecutive_days = $current_consecutive + 1;
            } else {
                $consecutive_days = 1;
            }
            
            $update_balance = $db->prepare("UPDATE user_balances SET total_amount = ?, consecutive_days = ?, last_spin_date = ?, can_withdraw = ? WHERE user_id = ?");
            $can_withdraw = ($consecutive_days >= 10 && $new_total >= 70) ? 1 : 0;
            $update_balance->bind_param("disii", $new_total, $consecutive_days, $today, $can_withdraw, $user_id);
            $update_balance->execute();
        }
        
        $can_withdraw = ($consecutive_days >= 10 && $new_total >= 70);
        
        echo json_encode([
            'success' => true,
            'prize' => $prize_text,
            'amount' => $won_amount,
            'total' => $new_total,
            'consecutive_days' => $consecutive_days,
            'can_withdraw' => $can_withdraw,
            'segment' => $selected_segment,  // Add this to tell frontend which segment won
            'debug' => 'Spin successful'
        ]);
        
        $db->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'debug' => 'Exception caught in spin handler'
        ]);
    }
    
    exit();
}

// Get current user data for display
try {
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if (!$db->connect_error) {
        // Create tables if they don't exist
        $create_balance_table = "CREATE TABLE IF NOT EXISTS user_balances (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            total_amount DECIMAL(10,2) DEFAULT 0,
            consecutive_days INT DEFAULT 0,
            last_spin_date DATE,
            can_withdraw BOOLEAN DEFAULT FALSE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $db->query($create_balance_table);
        
        // Get user balance
        $balance_query = $db->prepare("SELECT * FROM user_balances WHERE user_id = ?");
        $balance_query->bind_param("i", $user_id);
        $balance_query->execute();
        $balance_result = $balance_query->get_result();
        $user_balance = $balance_result->fetch_assoc();
        
        if ($user_balance) {
            $total_amount = $user_balance['total_amount'];
            $consecutive_days = $user_balance['consecutive_days'];
        }
        
        // Check if already spun today
        $check_spin = $db->prepare("SELECT * FROM spin_history WHERE user_id = ? AND spin_date = ?");
        if ($check_spin) {
            $check_spin->bind_param("is", $user_id, $today);
            $check_spin->execute();
            $already_spun = $check_spin->get_result()->num_rows > 0;
        } else {
            $already_spun = false;
        }
        
        // For testing, allow spinning (remove this in production)
        $can_spin = !$already_spun;
        
        if ($already_spun) {
            $message = "You have already spun today! Come back tomorrow.";
        }
        
        $db->close();
    }
} catch (Exception $e) {
    $message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin Wheel - Win Real Money!</title>
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
        padding: 20px;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, #FFD700, #FFA500);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.2);
        padding: 1rem;
        border-radius: 10px;
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

    .wheel-container {
        position: relative;
        display: inline-block;
        margin: 2rem 0;
    }

    .wheel {
        width: 320px;
        height: 320px;
        border-radius: 50%;
        position: relative;
        border: 8px solid #FFD700;
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
        transition: transform 4s cubic-bezier(0.23, 1, 0.32, 1);
        overflow: hidden;
    }

    .wheel-segment {
        position: absolute;
        width: 50%;
        height: 50%;
        transform-origin: 100% 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    .segment-text {
        position: absolute;
        top: 25%;
        left: 25%;
        transform: translate(-50%, -50%) rotate(-22.5deg);
        font-size: 18px;
        font-weight: 900;
        white-space: nowrap;
        pointer-events: none;
        text-align: center;
        letter-spacing: 1px;
    }

    .segment-1 {
        background: linear-gradient(135deg, #96CEB4, #66BB6A);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(135deg);
    }

    .segment-2 {
        background: linear-gradient(135deg, #4ECDC4, #26C6DA);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(45deg);
    }

    .segment-3 {
        background: linear-gradient(135deg, #45B7D1, #2196F3);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(90deg);
    }

    .segment-4 {
        background: linear-gradient(135deg, #96CEB4, #66BB6A);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(135deg);
    }

    .segment-5 {
        background: linear-gradient(135deg, #FFEAA7, #FFD54F);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(180deg);
    }

    .segment-5 .segment-text {
        color: #333;
        text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8);
    }

    .segment-6 {
        background: linear-gradient(135deg, rgb(155, 163, 168), rgb(19, 59, 102));
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(225deg);
    }

    .segment-7 {
        background: linear-gradient(135deg, #98D8C8, #4DB6AC);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(270deg);
    }

    .segment-8 {
        background: linear-gradient(135deg, #F7DC6F, #FBC02D);
        clip-path: polygon(100% 100%, 0% 100%, 29.3% 29.3%);
        transform: rotate(315deg);
    }

    .segment-8 .segment-text {
        color: #333;
        text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8);
    }

    .segment-text {
        position: absolute;
        top: 50%;
        left: 35%;
        /* More to the right to fit triangle shape */
        transform: translate(-50%, -50%);
        font-size: 18px;
        font-weight: 900;
        white-space: nowrap;
        pointer-events: none;
        text-align: center;
        letter-spacing: 1px;
        z-index: 5;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }


    .wheel-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, #FFD700, #FFA500);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #333;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        z-index: 10;
        font-size: 14px;
    }

    .pointer {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 30px solid #FF4757;
        z-index: 5;
    }

    .spin-button {
        background: linear-gradient(45deg, #FF6B6B, #FF4757);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 50px;
        cursor: pointer;
        margin: 1rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .spin-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 71, 87, 0.6);
    }

    .spin-button:disabled {
        background: rgb(0, 0, 0);
        cursor: not-allowed;
    }

    .message {
        margin: 1rem 0;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        font-size: 1.1rem;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .error {
        background: rgba(255, 107, 107, 0.3);
        border: 1px solid #FF6B6B;
    }

    .success {
        background: rgba(81, 207, 102, 0.3);
        border: 1px solid #51cf66;
    }

    .requirements {
        background: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 15px;
        margin-top: 2rem;
        text-align: left;
        font-size: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .requirements h3 {
        color: #FFD700;
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }

    .requirements ul {
        list-style: none;
        padding: 0;
    }

    .requirements li {
        padding: 0.8rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
    }

    .requirements li:last-child {
        border-bottom: none;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .wheel {
            width: 280px;
            height: 280px;
        }

        .segment-text {
            font-size: 16px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="title">🎲 Spin Wheel 🎲</h1>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="totalAmount">$<?php echo number_format($total_amount, 2); ?></div>
                <div class="stat-label">Total Winnings</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="consecutiveDays"><?php echo $consecutive_days; ?></div>
                <div class="stat-label">Consecutive Days</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo max(0, 10 - $consecutive_days); ?></div>
                <div class="stat-label">Days to Withdraw</div>
            </div>
        </div>

        <div class="wheel-container">
            <div class="pointer"></div>
            <div class="wheel" id="wheel">
                <div class="wheel-segment segment-1">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$10</div><br>
                </div>
                <div class="wheel-segment segment-2">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">
                        $8 </div><br>
                </div>
                <div class="wheel-segment segment-3">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$10</div>
                    <br>
                </div>
                <div class="wheel-segment segment-4">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$5</div><br>
                </div>
                <div class="wheel-segment segment-5">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$15</div>
                    <br>
                </div>
                <div class="wheel-segment segment-6">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$6</div>
                    <br>
                </div>
                <div class="wheel-segment segment-7">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">$20</div>
                    <br>
                </div>
                <div class="wheel-segment segment-8">
                    <div class="segment-text" style="transform: translate(-50%, -50%) rotate(-135deg);">try</div><br>
                </div>
                <div class="wheel-center">SPIN</div>
            </div>
        </div>

        <button class="spin-button" id="spinBtn" onclick="spinWheel()" <?php echo !$can_spin ? 'disabled' : ''; ?>>
            <?php echo $can_spin ? '🎲 SPIN THE WHEEL 🎲' : 'Already Spin Today'; ?>
        </button>

        <div class="message" id="result">
            <?php echo $message ?: "Ready to spin! Good luck! 🍀"; ?>
        </div>

        <div class="requirements">
            <h3>📋 How It Works:</h3>
            <ul>
                <li>🎯 Spin once per day to win cash prizes</li>
                <li>🛒 Make daily purchases to stay eligible</li>
                <li>📅 Spin for 10 consecutive days</li>
                <li>💰 Reach $70+ total to unlock withdrawal</li>
                <li>🏆 Win up to $20 per spin!</li>
            </ul>
        </div>
    </div>

    <script>
    let isSpinning = false;

    function spinWheel() {
        if (isSpinning) {
            return;
        }

        isSpinning = true;

        const spinBtn = document.getElementById('spinBtn');
        const wheel = document.getElementById('wheel');
        const result = document.getElementById('result');

        spinBtn.disabled = true;
        spinBtn.textContent = '🌪 SPINNING... 🌪';
        result.textContent = 'Spinning... Good luck! 🤞';
        result.className = 'message';

        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'spin');

        fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Calculate the correct rotation to land on the winning segment
                    // Each segment is 45 degrees (360/8 = 45)
                    // Segment 0 is at the top, so we need to account for that
                    const segmentAngle = 45; // degrees per segment
                    const segmentOffset = data.segment * segmentAngle;

                    // Add multiple full rotations for dramatic effect (5-8 full spins)
                    const fullRotations = (Math.random() * 3 + 5) * 360;

                    // Calculate final position - we want the pointer to point to the center of the winning segment
                    // The pointer points up, so segment 0 should be at 0 degrees
                    // We need to rotate so the winning segment is at the top
                    const finalRotation = fullRotations - segmentOffset;

                    wheel.style.transform = `rotate(${finalRotation}deg)`;

                    setTimeout(() => {
                        result.innerHTML = `
                            🎉 Congratulations! 🎉<br>
                            You won: <strong>$${data.amount} ${data.prize}</strong><br>
                            Total Balance: <strong>$${parseFloat(data.total).toFixed(2)}</strong><br>
                            Consecutive Days: <strong>${data.consecutive_days}</strong>
                        `;
                        result.className = 'message success';

                        // Update display
                        document.getElementById('totalAmount').textContent =
                            `$${parseFloat(data.total).toFixed(2)}`;
                        document.getElementById('consecutiveDays').textContent = data.consecutive_days;

                        spinBtn.textContent = 'Already Spun Today';
                        isSpinning = false;
                    }, 4000);

                } else {
                    setTimeout(() => {
                        result.textContent = `Error: ${data.error}`;
                        result.className = 'message error';
                        spinBtn.disabled = false;
                        spinBtn.textContent = '🎲 SPIN THE WHEEL 🎲';
                        isSpinning = false;
                    }, 2000);
                }
            })
            .catch(error => {
                setTimeout(() => {
                    result.textContent = `Connection error: ${error.message}`;
                    result.className = 'message error';
                    spinBtn.disabled = false;
                    spinBtn.textContent = '🎲 SPIN THE WHEEL 🎲';
                    isSpinning = false;
                }, 2000);
            });
    }
    </script>
</body>

</html>