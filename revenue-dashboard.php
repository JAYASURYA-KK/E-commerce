<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configurations - UPDATE THESE WITH YOUR ACTUAL CREDENTIALS
$onlinefood_config = [
    'host' => 'localhost',
    'username' => 'root',  // Change this
    'password' => '',      // Change this
    'database' => 'onlinefoodphp'
];

$projectmeteor_config = [
    'host' => 'localhost',
    'username' => 'root',  // Change this
    'password' => '',      // Change this
    'database' => 'projectmeteor'
];

// Function to connect to database
function connectDB($config) {
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Get date filter from URL parameters
$filter_type = $_GET['filter'] ?? 'all';
$selected_month = $_GET['month'] ?? date('Y-m');
$selected_date = $_GET['date'] ?? date('Y-m-d');

// Build date condition based on filter
function getDateCondition($filter_type, $selected_month, $selected_date, $date_column = 'date') {
    switch($filter_type) {
        case 'today':
            return "DATE($date_column) = CURDATE()";
        case 'yesterday':
            return "DATE($date_column) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        case 'this_week':
            return "YEARWEEK($date_column, 1) = YEARWEEK(CURDATE(), 1)";
        case 'this_month':
            return "YEAR($date_column) = YEAR(CURDATE()) AND MONTH($date_column) = MONTH(CURDATE())";
        case 'last_month':
            return "YEAR($date_column) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH($date_column) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
        case 'custom_month':
            return "DATE_FORMAT($date_column, '%Y-%m') = '$selected_month'";
        case 'custom_date':
            return "DATE($date_column) = '$selected_date'";
        default:
            return "1=1"; // No filter
    }
}

// Get Food Revenue from users_orders table
function getFoodRevenue($filter_type, $selected_month, $selected_date) {
    global $onlinefood_config;
    try {
        $conn = connectDB($onlinefood_config);
        $date_condition = getDateCondition($filter_type, $selected_month, $selected_date, 'date');
        
        // Food keywords - comprehensive list for Indian and international food
        $food_keywords = [
            'food', 'meal', 'chicken', 'rice', 'curry', 'pizza', 'burger', 'sandwich',
            'noodles', 'pasta', 'soup', 'salad', 'biryani', 'fish', 'mutton', 'beef',
            'vegetable', 'dal', 'roti', 'bread', 'chettinad', 'masala', 'fry', 'gravy',
            'samosa', 'dosa', 'idli', 'vada', 'paratha', 'chapati', 'sabzi', 'paneer',
            'kebab', 'tandoor', 'korma', 'vindaloo', 'tikka', 'biriyani', 'pulao',
            'breakfast', 'lunch', 'dinner', 'snack', 'drink', 'juice', 'coffee', 'tea'
        ];
        
        $food_conditions = [];
        foreach ($food_keywords as $keyword) {
            $food_conditions[] = "LOWER(title) LIKE '%$keyword%'";
        }
        $food_condition = "(" . implode(" OR ", $food_conditions) . ")";
        
        $sql = "SELECT 
                    SUM(price * quantity) as total_revenue, 
                    COUNT(*) as total_orders,
                    AVG(price * quantity) as avg_order_value,
                    GROUP_CONCAT(DISTINCT title SEPARATOR ', ') as sample_items
                FROM users_orders 
                WHERE $food_condition
                AND (status != 'cancelled' OR status IS NULL)
                AND $date_condition";
        
        $result = $conn->query($sql);
        $data = ['revenue' => 0, 'orders' => 0, 'avg_value' => 0, 'sample_items' => ''];
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'revenue' => floatval($row['total_revenue'] ?? 0),
                'orders' => intval($row['total_orders'] ?? 0),
                'avg_value' => floatval($row['avg_order_value'] ?? 0),
                'sample_items' => $row['sample_items'] ?? ''
            ];
        }
        
        $conn->close();
        return $data;
    } catch (Exception $e) {
        return ['revenue' => 0, 'orders' => 0, 'avg_value' => 0, 'sample_items' => 'Error: ' . $e->getMessage()];
    }
}

// Get Shopping Revenue from order_items table
function getShoppingRevenue($filter_type, $selected_month, $selected_date) {
    global $onlinefood_config;
    try {
        $conn = connectDB($onlinefood_config);
        
        // For order_items table, we need to join with users_orders to get the date
        // since order_items references orders table, but we're using users_orders
        $date_condition = getDateCondition($filter_type, $selected_month, $selected_date, 'uo.date');
        
        // First, let's try to get all order_items data
        $sql = "SELECT 
                    SUM(oi.subtotal) as total_revenue, 
                    COUNT(*) as total_orders,
                    AVG(oi.subtotal) as avg_order_value,
                    GROUP_CONCAT(DISTINCT oi.product_name SEPARATOR ', ') as sample_items
                FROM order_items oi
                LEFT JOIN users_orders uo ON oi.order_id = uo.o_id
                WHERE $date_condition";
        
        $result = $conn->query($sql);
        $data = ['revenue' => 0, 'orders' => 0, 'avg_value' => 0, 'sample_items' => ''];
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $data = [
                'revenue' => floatval($row['total_revenue'] ?? 0),
                'orders' => intval($row['total_orders'] ?? 0),
                'avg_value' => floatval($row['avg_order_value'] ?? 0),
                'sample_items' => $row['sample_items'] ?? ''
            ];
        }
        
        // If no data from join, try order_items alone
        if ($data['revenue'] == 0) {
            $sql = "SELECT 
                        SUM(subtotal) as total_revenue, 
                        COUNT(*) as total_orders,
                        AVG(subtotal) as avg_order_value,
                        GROUP_CONCAT(DISTINCT product_name SEPARATOR ', ') as sample_items
                    FROM order_items";
            
            if ($filter_type != 'all') {
                // If we can't join with users_orders for date, we'll get all data
                // You might need to add a date column to order_items table
                $sql .= " WHERE 1=1"; // Placeholder - modify based on your order_items structure
            }
            
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $data = [
                    'revenue' => floatval($row['total_revenue'] ?? 0),
                    'orders' => intval($row['total_orders'] ?? 0),
                    'avg_value' => floatval($row['avg_order_value'] ?? 0),
                    'sample_items' => $row['sample_items'] ?? ''
                ];
            }
        }
        
        $conn->close();
        return $data;
    } catch (Exception $e) {
        return ['revenue' => 0, 'orders' => 0, 'avg_value' => 0, 'sample_items' => 'Error: ' . $e->getMessage()];
    }
}

// Get Travel Revenue from booking_amounts table (projectmeteor database)
function getTravelRevenue($filter_type, $selected_month, $selected_date) {
    global $projectmeteor_config;
    try {
        $conn = connectDB($projectmeteor_config);
        $date_condition = getDateCondition($filter_type, $selected_month, $selected_date, 'created_at');
        
        $sql = "SELECT 
                    SUM(CASE WHEN booking_type = 'flight' THEN total_amount ELSE 0 END) as flight_revenue,
                    SUM(CASE WHEN booking_type = 'hotel' THEN total_amount ELSE 0 END) as hotel_revenue,
                    SUM(CASE WHEN booking_type = 'train' THEN total_amount ELSE 0 END) as train_revenue,
                    SUM(total_amount) as total_travel_revenue,
                    COUNT(*) as total_bookings,
                    COUNT(CASE WHEN booking_type = 'flight' THEN 1 END) as flight_bookings,
                    COUNT(CASE WHEN booking_type = 'hotel' THEN 1 END) as hotel_bookings,
                    COUNT(CASE WHEN booking_type = 'train' THEN 1 END) as train_bookings,
                    AVG(total_amount) as avg_booking_value
                FROM booking_amounts 
                WHERE (status = 'confirmed' OR status = 'completed' OR status = 'paid')
                AND $date_condition";
        
        $result = $conn->query($sql);
        $revenue_data = [
            'flight' => 0, 'hotel' => 0, 'train' => 0, 'total' => 0,
            'bookings' => 0, 'flight_bookings' => 0, 'hotel_bookings' => 0, 
            'train_bookings' => 0, 'avg_value' => 0
        ];
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $revenue_data = [
                'flight' => floatval($row['flight_revenue'] ?? 0),
                'hotel' => floatval($row['hotel_revenue'] ?? 0),
                'train' => floatval($row['train_revenue'] ?? 0),
                'total' => floatval($row['total_travel_revenue'] ?? 0),
                'bookings' => intval($row['total_bookings'] ?? 0),
                'flight_bookings' => intval($row['flight_bookings'] ?? 0),
                'hotel_bookings' => intval($row['hotel_bookings'] ?? 0),
                'train_bookings' => intval($row['train_bookings'] ?? 0),
                'avg_value' => floatval($row['avg_booking_value'] ?? 0)
            ];
        }
        
        $conn->close();
        return $revenue_data;
    } catch (Exception $e) {
        return [
            'flight' => 0, 'hotel' => 0, 'train' => 0, 'total' => 0,
            'bookings' => 0, 'flight_bookings' => 0, 'hotel_bookings' => 0, 
            'train_bookings' => 0, 'avg_value' => 0
        ];
    }
}

// Get monthly revenue data for chart
function getMonthlyRevenue() {
    $monthly_data = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));
        
        $food_data = getFoodRevenue('custom_month', $month, '');
        $shopping_data = getShoppingRevenue('custom_month', $month, '');
        $travel_data = getTravelRevenue('custom_month', $month, '');
        
        $monthly_data[] = [
            'month' => $month_name,
            'food' => $food_data['revenue'],
            'shopping' => $shopping_data['revenue'],
            'travel' => $travel_data['total'],
            'total' => $food_data['revenue'] + $shopping_data['revenue'] + $travel_data['total']
        ];
    }
    
    return $monthly_data;
}

// Get revenue data based on current filter
$food_data = getFoodRevenue($filter_type, $selected_month, $selected_date);
$shopping_data = getShoppingRevenue($filter_type, $selected_month, $selected_date);
$travel_data = getTravelRevenue($filter_type, $selected_month, $selected_date);

$food_revenue = $food_data['revenue'];
$shopping_revenue = $shopping_data['revenue'];
$travel_revenue = $travel_data['total'];
$total_revenue = $food_revenue + $shopping_revenue + $travel_revenue;

// Get monthly data for trend chart
$monthly_data = getMonthlyRevenue();

// Calculate percentages
$food_percentage = $total_revenue > 0 ? ($food_revenue / $total_revenue) * 100 : 0;
$shopping_percentage = $total_revenue > 0 ? ($shopping_revenue / $total_revenue) * 100 : 0;
$travel_percentage = $total_revenue > 0 ? ($travel_revenue / $total_revenue) * 100 : 0;

// Get filter display name
function getFilterDisplayName($filter_type, $selected_month, $selected_date) {
    switch($filter_type) {
        case 'today': return 'Today';
        case 'yesterday': return 'Yesterday';
        case 'this_week': return 'This Week';
        case 'this_month': return 'This Month';
        case 'last_month': return 'Last Month';
        case 'custom_month': return date('F Y', strtotime($selected_month . '-01'));
        case 'custom_date': return date('F j, Y', strtotime($selected_date));
        default: return 'All Time';
    }
}

$filter_display = getFilterDisplayName($filter_type, $selected_month, $selected_date);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Dashboard - <?php echo $filter_display; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .debug-section {
        background: #f8f9fa;
        padding: 20px;
        margin: 20px;
        border-radius: 10px;
        border-left: 4px solid #007bff;
    }

    .debug-info {
        font-family: monospace;
        font-size: 0.9rem;
        background: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 25px 30px;
        border-bottom: 1px solid #dee2e6;
    }

    .filter-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
        justify-content: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
    }

    .filter-controls select,
    .filter-controls input {
        padding: 10px 15px;
        border: 2px solid #ced4da;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: border-color 0.3s ease;
    }

    .filter-controls select:focus,
    .filter-controls input:focus {
        outline: none;
        border-color: #007bff;
    }

    .filter-btn {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 20px;
        transition: transform 0.2s ease;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
    }

    .current-filter {
        text-align: center;
        font-size: 1.2rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        padding: 30px;
        background: #f8f9fa;
    }

    .stat-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .stat-card.food {
        --card-color: #e74c3c;
        --card-color-light: #ff6b6b;
    }

    .stat-card.shopping {
        --card-color: #3498db;
        --card-color-light: #74b9ff;
    }

    .stat-card.travel {
        --card-color: #2ecc71;
        --card-color-light: #55efc4;
    }

    .stat-card.total {
        --card-color: #9b59b6;
        --card-color-light: #a29bfe;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: var(--card-color);
    }

    .stat-label {
        font-size: 1.2rem;
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .percentage {
        font-size: 1rem;
        color: #7f8c8d;
        margin-top: 8px;
        font-weight: 500;
    }

    .stat-details {
        font-size: 0.85rem;
        color: #95a5a6;
        margin-top: 12px;
        line-height: 1.5;
        padding-top: 12px;
        border-top: 1px solid #ecf0f1;
        text-align: left;
    }

    .charts-section {
        padding: 40px 30px;
        background: white;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 30px;
    }

    .chart-container {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        height: 450px;
    }

    .chart-container.full-width {
        grid-column: 1 / -1;
        height: 400px;
    }

    .chart-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
        color: #2c3e50;
    }

    .chart-canvas {
        width: 100% !important;
        height: calc(100% - 60px) !important;
    }

    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-value {
            font-size: 2rem;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Revenue Dashboard</h1>
            <p>Food • Shopping • Travel Revenue Analytics</p>
        </div>



        <div class="filter-section">
            <div class="current-filter">
                📊 Showing data for: <strong><?php echo $filter_display; ?></strong>
            </div>

            <form method="GET" class="filter-controls">
                <div class="filter-group">
                    <label>📅 Quick Filters:</label>
                    <select name="filter" onchange="toggleCustomInputs(this.value)">
                        <option value="all" <?php echo $filter_type == 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="today" <?php echo $filter_type == 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="yesterday" <?php echo $filter_type == 'yesterday' ? 'selected' : ''; ?>>Yesterday
                        </option>
                        <option value="this_week" <?php echo $filter_type == 'this_week' ? 'selected' : ''; ?>>This Week
                        </option>
                        <option value="this_month" <?php echo $filter_type == 'this_month' ? 'selected' : ''; ?>>This
                            Month</option>
                        <option value="last_month" <?php echo $filter_type == 'last_month' ? 'selected' : ''; ?>>Last
                            Month</option>
                        <option value="custom_month" <?php echo $filter_type == 'custom_month' ? 'selected' : ''; ?>>
                            Custom Month</option>
                        <option value="custom_date" <?php echo $filter_type == 'custom_date' ? 'selected' : ''; ?>>
                            Custom Date</option>
                    </select>
                </div>

                <div class="filter-group" id="month-input"
                    style="display: <?php echo $filter_type == 'custom_month' ? 'block' : 'none'; ?>;">
                    <label>📅 Select Month:</label>
                    <input type="month" name="month" value="<?php echo $selected_month; ?>">
                </div>

                <div class="filter-group" id="date-input"
                    style="display: <?php echo $filter_type == 'custom_date' ? 'block' : 'none'; ?>;">
                    <label>📅 Select Date:</label>
                    <input type="date" name="date" value="<?php echo $selected_date; ?>">
                </div>

                <button type="submit" class="filter-btn">Apply Filter</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card food">
                <div class="stat-value">$<?php echo number_format($food_revenue, 2); ?></div>
                <div class="stat-label">🍕 Food Revenue</div>
                <div class="percentage"><?php echo number_format($food_percentage, 1); ?>% of total</div>
                <div class="stat-details">
                    <strong>Source:</strong> users_orders table<br>
                    <strong>Orders:</strong> <?php echo number_format($food_data['orders']); ?><br>
                    <strong>Avg Order:</strong> $<?php echo number_format($food_data['avg_value'], 2); ?>
                </div>
            </div>

            <div class="stat-card shopping">
                <div class="stat-value">$<?php echo number_format($shopping_revenue, 2); ?></div>
                <div class="stat-label">🛍️ Shopping Revenue</div>
                <div class="percentage"><?php echo number_format($shopping_percentage, 1); ?>% of total</div>
                <div class="stat-details">
                    <strong>Source:</strong> order_items table<br>
                    <strong>Items:</strong> <?php echo number_format($shopping_data['orders']); ?><br>
                    <strong>Avg Item:</strong> $<?php echo number_format($shopping_data['avg_value'], 2); ?>
                </div>
            </div>

            <div class="stat-card travel">
                <div class="stat-value">$<?php echo number_format($travel_revenue, 2); ?></div>
                <div class="stat-label">✈️ Travel Revenue</div>
                <div class="percentage"><?php echo number_format($travel_percentage, 1); ?>% of total</div>
                <div class="stat-details">
                    <strong>Source:</strong> booking_amounts table<br>
                    <strong>✈️ Flights:</strong> $<?php echo number_format($travel_data['flight'], 2); ?>
                    (<?php echo $travel_data['flight_bookings']; ?>)<br>
                    <strong>🏨 Hotels:</strong> $<?php echo number_format($travel_data['hotel'], 2); ?>
                    (<?php echo $travel_data['hotel_bookings']; ?>)<br>
                    <strong>🚂 Trains:</strong> $<?php echo number_format($travel_data['train'], 2); ?>
                    (<?php echo $travel_data['train_bookings']; ?>)
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">💰 Total Revenue</div>
                <div class="percentage">100% combined revenue</div>
                <div class="stat-details">
                    <strong>Total Transactions:</strong>
                    <?php echo number_format($food_data['orders'] + $shopping_data['orders'] + $travel_data['bookings']); ?><br>
                    <strong>Average Transaction:</strong>
                    $<?php echo ($food_data['orders'] + $shopping_data['orders'] + $travel_data['bookings']) > 0 ? number_format($total_revenue / ($food_data['orders'] + $shopping_data['orders'] + $travel_data['bookings']), 2) : '0.00'; ?>
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-title">📊 Revenue Distribution</div>
                    <canvas id="pieChart" class="chart-canvas"></canvas>
                </div>

                <div class="chart-container">
                    <div class="chart-title">📈 Category Comparison</div>
                    <canvas id="barChart" class="chart-canvas"></canvas>
                </div>

                <div class="chart-container full-width">
                    <div class="chart-title">📈 12-Month Revenue Trend</div>
                    <canvas id="lineChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Revenue data from PHP - ensure all values are properly formatted
    const revenueData = {
        food: <?php echo json_encode(max(0, floatval($food_revenue))); ?>,
        shopping: <?php echo json_encode(max(0, floatval($shopping_revenue))); ?>,
        travel: <?php echo json_encode(max(0, floatval($travel_revenue))); ?>
    };

    const monthlyData = <?php echo json_encode($monthly_data); ?>;

    // Debug log
    console.log('Revenue Data:', revenueData);
    console.log('Total Revenue:', revenueData.food + revenueData.shopping + revenueData.travel);

    // Toggle custom input visibility
    function toggleCustomInputs(filterType) {
        document.getElementById('month-input').style.display = filterType === 'custom_month' ? 'block' : 'none';
        document.getElementById('date-input').style.display = filterType === 'custom_date' ? 'block' : 'none';
    }

    // Chart.js configuration
    Chart.defaults.font.family = 'Segoe UI';
    Chart.defaults.font.size = 12;

    // Color scheme
    const colors = {
        food: {
            primary: '#e74c3c',
            light: 'rgba(231, 76, 60, 0.8)',
            lighter: 'rgba(231, 76, 60, 0.2)'
        },
        shopping: {
            primary: '#3498db',
            light: 'rgba(52, 152, 219, 0.8)',
            lighter: 'rgba(52, 152, 219, 0.2)'
        },
        travel: {
            primary: '#2ecc71',
            light: 'rgba(46, 204, 113, 0.8)',
            lighter: 'rgba(46, 204, 113, 0.2)'
        }
    };

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['🍕 Food', '🛍️ Shopping', '✈️ Travel'],
            datasets: [{
                data: [revenueData.food, revenueData.shopping, revenueData.travel],
                backgroundColor: [colors.food.light, colors.shopping.light, colors.travel.light],
                borderColor: [colors.food.primary, colors.shopping.primary, colors.travel.primary],
                borderWidth: 3,
                hoverOffset: 15,
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = revenueData.food + revenueData.shopping + revenueData.travel;
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return `${context.label}: $${context.parsed.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');

    const foodGradient = barCtx.createLinearGradient(0, 0, 0, 400);
    foodGradient.addColorStop(0, colors.food.light);
    foodGradient.addColorStop(1, colors.food.lighter);

    const shoppingGradient = barCtx.createLinearGradient(0, 0, 0, 400);
    shoppingGradient.addColorStop(0, colors.shopping.light);
    shoppingGradient.addColorStop(1, colors.shopping.lighter);

    const travelGradient = barCtx.createLinearGradient(0, 0, 0, 400);
    travelGradient.addColorStop(0, colors.travel.light);
    travelGradient.addColorStop(1, colors.travel.lighter);

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['🍕 Food', '🛍️ Shopping', '✈️ Travel'],
            datasets: [{
                data: [revenueData.food, revenueData.shopping, revenueData.travel],
                backgroundColor: [foodGradient, shoppingGradient, travelGradient],
                borderColor: [colors.food.primary, colors.shopping.primary, colors.travel.primary],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');

    const foodLineGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
    foodLineGradient.addColorStop(0, colors.food.lighter);
    foodLineGradient.addColorStop(1, 'rgba(231, 76, 60, 0.05)');

    const shoppingLineGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
    shoppingLineGradient.addColorStop(0, colors.shopping.lighter);
    shoppingLineGradient.addColorStop(1, 'rgba(52, 152, 219, 0.05)');

    const travelLineGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
    travelLineGradient.addColorStop(0, colors.travel.lighter);
    travelLineGradient.addColorStop(1, 'rgba(46, 204, 113, 0.05)');

    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                    label: '🍕 Food',
                    data: monthlyData.map(item => item.food),
                    borderColor: colors.food.primary,
                    backgroundColor: foodLineGradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colors.food.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                },
                {
                    label: '🛍️ Shopping',
                    data: monthlyData.map(item => item.shopping),
                    borderColor: colors.shopping.primary,
                    backgroundColor: shoppingLineGradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colors.shopping.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                },
                {
                    label: '✈️ Travel',
                    data: monthlyData.map(item => item.travel),
                    borderColor: colors.travel.primary,
                    backgroundColor: travelLineGradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: colors.travel.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    </script>
</body>

</html>