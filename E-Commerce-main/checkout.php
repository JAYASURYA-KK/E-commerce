<?php   
include_once('./includes/headerNav.php');
include("connection/connect.php"); 


// Check if user is logged in - COMPULSORY
if(empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";
$error = "";

// Handle form submission when Pay button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proceed_to_pay'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $house_number = trim($_POST['house_number']);
    $street_road = trim($_POST['street_road']);
    $town_city = trim($_POST['town_city']);
    $post_code = trim($_POST['post_code']);
    $country = trim($_POST['country']);
    $contact_number = trim($_POST['contact_number']);
    $email_address = trim($_POST['email_address']);
    
    // Validate all fields
    if (empty($first_name) || empty($last_name) || empty($house_number) || 
        empty($street_road) || empty($town_city) || empty($post_code) || 
        empty($country) || empty($contact_number) || empty($email_address)) {
        $error = "Please fill all fields.";
    } else {
        // Calculate total from cart
        $total_amount = 0;
        if (isset($_SESSION['mycart']) && !empty($_SESSION['mycart'])) {
            foreach ($_SESSION['mycart'] as $item) {
                $total_amount += $item['price'] * $item['product_qty'];
            }
        }
        
        if ($total_amount > 0) {
            // Store checkout details in database
            $order_query = "INSERT INTO orders (user_id, total_amount, first_name, last_name, 
                           house_number, street_road, town_city, post_code, country, 
                           contact_number, email_address, payment_status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $stmt = mysqli_prepare($db, $order_query);
            mysqli_stmt_bind_param($stmt, "idsssssssss", $user_id, $total_amount, $first_name, 
                                 $last_name, $house_number, $street_road, $town_city, 
                                 $post_code, $country, $contact_number, $email_address);
            
            if (mysqli_stmt_execute($stmt)) {
                $order_id = mysqli_insert_id($db);
                
                // Store order items
                foreach ($_SESSION['mycart'] as $item) {
                    $subtotal = $item['price'] * $item['product_qty'];
                    $item_query = "INSERT INTO order_items (order_id, product_id, product_name, 
                                  product_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
                    
                    $item_stmt = mysqli_prepare($db, $item_query);
                    mysqli_stmt_bind_param($item_stmt, "iisdid", $order_id, $item['product_id'], 
                                         $item['name'], $item['price'], $item['product_qty'], $subtotal);
                    mysqli_stmt_execute($item_stmt);
                }
                
                // Store order ID in session for payment page
                $_SESSION['pending_order_id'] = $order_id;
                
                // Redirect to payment page
                header("Location: payment.php");
                exit();
            } else {
                $error = "Error processing order. Please try again.";
            }
        } else {
            $error = "Your cart is empty!";
        }
    }
}
?>

<div class="overlay" data-overlay></div>

<header>
  <?php require_once './includes/topheadactions.php'; ?>
  <?php require_once './includes/mobilenav.php'; ?>

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        
        :root{
            --main-maroon: #CE5959;
            --deep-maroon: #89375F;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .appointments-section {
            width: 80%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .appointment-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .appointment-head {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--main-maroon);
        }

        .appointment-line {
            width: 160px;
            height: 3px;
            border-radius: 10px;
            background-color: var(--main-maroon);
            display: inline-block;
        }

        .child-detail-inner {
            width: 100%;
            display: flex;
            margin-top: 10px;
            justify-content: space-between;
            gap: 20px;
        }

        .child-fields1, .child-fields3, .child-fields4, .child-fields5, 
        .child-fields6, .child-fields7, .child-fields8, .child-fields9, .Address-field {
            width: 49%;
            height: 55px;
            border: 1px solid var(--main-maroon);
            border-radius: 5px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #FFFFFF;
            position: relative;
            box-shadow: 2px 2px 2px rgb(185, 184, 184);
        }

        .Address-field {
            width: 100%;
        }

        .child-fields1::before { content: "First Name"; }
        .child-fields3::before { content: "Last Name"; }
        .child-fields4::before { content: "House Number or Name"; }
        .child-fields5::before { content: "Street or Road"; }
        .child-fields6::before { content: "Town or City"; }
        .child-fields7::before { content: "Post Code"; }
        .child-fields8::before { content: "Contact Number"; }
        .child-fields9::before { content: "Email Address"; }
        .Address-field::before { content: "Country Name"; }

        .child-fields1::before, .child-fields3::before, .child-fields4::before, 
        .child-fields5::before, .child-fields6::before, .child-fields7::before, 
        .child-fields8::before, .child-fields9::before, .Address-field::before {
            position: absolute;
            top: -10px;
            background-image: linear-gradient(#FFFCF6, #FFFFFF);
            padding-left: 4px;
            padding-right: 4px;
            color: var(--main-maroon);
            font-weight: 600;
            font-size: 13px;
        }

        .child-fields1 input, .child-fields3 input, .child-fields4 input, 
        .child-fields5 input, .child-fields6 input, .child-fields7 input, 
        .child-fields8 input, .child-fields9 input, .Address-field input {
            color: #000000;
            font-weight: 700;
            width: 100%;
            background-color: transparent;
            border: none;
            outline: none;
        }

        .child-register-btn {
            text-align: center;
            margin-top: 20px;
        }

        .child-register-btn button {
            width: 350px;
            height: 60px;
            background-color: var(--main-maroon);
            box-shadow: 0px 0px 4px #615f5f;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 19px;
            font-weight: 600;
        }

        .child-register-btn button:hover {
            background-color: var(--deep-maroon);
        }

        .error-ms {
            color: var(--main-maroon);
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }

        .user-info {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media screen and (max-width: 794px) {
            .appointments-section {
                width: 90%;
                padding: 20px;
            }
            
            .child-detail-inner {
                flex-direction: column;
            }
            
            .child-fields1, .child-fields3, .child-fields4, .child-fields5, 
            .child-fields6, .child-fields7, .child-fields8, .child-fields9 {
                width: 100%;
            }
            
            .child-register-btn button {
                width: 100%;
            }
        }
    </style>
</header>

<body>
    <div class="appointments-section">
        <div class="appointment-heading">
            <p class="appointment-head">CheckOut</p>
            <span class="appointment-line"></span>
        </div>

        <?php
        // Get user info from existing users table
        $user_query = "SELECT username, email FROM users WHERE u_id = ?";
        $user_stmt = mysqli_prepare($db, $user_query);
        mysqli_stmt_bind_param($user_stmt, "i", $user_id);
        mysqli_stmt_execute($user_stmt);
        $user_result = mysqli_stmt_get_result($user_stmt);
        $user_info = mysqli_fetch_assoc($user_result);
        ?>

        <div class="user-info">
            <strong>Logged in as:</strong> <?php echo htmlspecialchars($user_info['username']); ?> 
            <?php if($user_info['email']): ?>
                (<?php echo htmlspecialchars($user_info['email']); ?>)
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-ms"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="child-detail-inner">
                <div class="child-fields1">
                    <input type="text" name="first_name" placeholder="First Name" required>
                </div>
                <div class="child-fields3">
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
            </div>

            <div class="child-detail-inner">
                <div class="child-fields4">
                    <input type="text" name="house_number" placeholder="House Number or Name" required>
                </div>
                <div class="child-fields5">
                    <input type="text" name="street_road" placeholder="Street or Road" required>
                </div>
            </div>

            <div class="child-detail-inner">
                <div class="child-fields6">
                    <input type="text" name="town_city" placeholder="Town or City" required>
                </div>
                <div class="child-fields7">
                    <input type="text" name="post_code" placeholder="Post Code" required>
                </div>
            </div>

            <div class="child-detail-inner">
                <div class="Address-field">
                    <input type="text" name="country" placeholder="Country Name" required>
                </div>
            </div>

            <div class="child-detail-inner">
                <div class="child-fields8">
                    <input type="tel" name="contact_number" placeholder="Contact Number" required>
                </div>
                <div class="child-fields9">
                    <input type="email" name="email_address" placeholder="Email Address" required>
                </div>
            </div>

            <div class="child-register-btn">
                <button type="submit" name="proceed_to_pay">Proceed To Pay</button>
            </div>
        </form>
    </div>
</body>

<?php require_once './includes/footer.php'; ?>