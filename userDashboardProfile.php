<?php
// Session and authentication check
session_start();

// Check if user is logged in
if(!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    // Redirect to login page if not logged in
    header("Location:Online-Food-Ordering-System-in-PHP-main\login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlinefoodphp";

// Creating a connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking if successfully connected to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Query to get user information including image
$profileSQL = "SELECT * FROM `users` WHERE u_id = ?";
$stmt = $conn->prepare($profileSQL);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$profileQuery = $stmt->get_result();

// Check if user exists
if ($profileQuery->num_rows > 0) {
    $row = $profileQuery->fetch_assoc();
} else {
    // User not found - destroy session and redirect to login
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit();
}

// Function to display image from database (BLOB or base64)
function displayProfileImage($imageData, $mimeType = null) {
    if (empty($imageData)) {
        return null;
    }
    
    // Check if it's base64 encoded
    if (base64_encode(base64_decode($imageData, true)) === $imageData) {
        $mimeType = $mimeType ?: 'image/jpeg';
        return "data:$mimeType;base64,$imageData";
    }
    
    // If it's a file path, check if file exists
    if (is_string($imageData) && file_exists($imageData)) {
        return $imageData;
    }
    
    return null;
}

// Get profile image
$profileImageSrc = displayProfileImage($row['user_image'] ?? null, $row['image_mime_type'] ?? null);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile Dashboard | JS Weby</title>

    <link rel='stylesheet'
        href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Roboto', sans-serif;
        min-height: 100vh;
        color: #333;
        line-height: 1.6;
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 0;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 2rem;
    }

    .logo {
        font-size: 2rem;
        font-weight: bold;
        background: linear-gradient(45deg, #fff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .logo:hover {
        transform: scale(1.05);
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 2rem;
    }

    .nav-menu a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }

    .nav-menu a:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .container {
        max-width: 1000px;
        margin: 120px auto 50px;
        padding: 0 20px;
    }

    .profile-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        backdrop-filter: blur(10px);
        animation: slideUp 0.6s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border: 4px solid rgba(255, 255, 255, 0.3);
        position: relative;
        z-index: 1;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .profile-avatar .default-icon {
        color: rgba(255, 255, 255, 0.8);
        font-size: 3rem;
    }

    .profile-name {
        font-size: 2rem;
        font-weight: 300;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.6s ease 0.2s both;
    }

    .profile-username {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.6s ease 0.3s both;
    }

    .profile-content {
        padding: 40px;
    }

    .profile-section {
        margin-bottom: 30px;
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    .profile-section:nth-child(1) {
        animation-delay: 0.4s;
    }

    .profile-section:nth-child(2) {
        animation-delay: 0.5s;
    }

    .profile-section:nth-child(3) {
        animation-delay: 0.6s;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 50px;
        height: 2px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        animation: expandWidth 0.8s ease forwards;
    }

    @keyframes expandWidth {
        from {
            width: 0;
        }

        to {
            width: 50px;
        }
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .info-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        border-radius: 15px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    .info-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
    }

    .info-item:hover::before {
        left: 100%;
    }

    .info-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border-left-color: #764ba2;
    }

    .info-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .info-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .info-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .info-item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-label::before {
        content: '';
        width: 4px;
        height: 4px;
        background: #667eea;
        border-radius: 50%;
    }

    .info-value {
        font-size: 1.1rem;
        color: #333;
        font-weight: 400;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .address-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px;
        border-radius: 15px;
        border: 1px solid #e0e0e0;
        position: relative;
        overflow: hidden;
    }

    .address-section::before {
        content: '📍';
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 1.5rem;
        opacity: 0.3;
    }

    .stats-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .stat-item {
        display: inline-block;
        margin: 0 20px;
        position: relative;
        z-index: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 300;
        display: block;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        animation: fadeInUp 0.6s ease 0.8s both;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: transparent;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-secondary:hover {
        background: #667eea;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    /* Image status indicator */
    .image-status {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid white;
        z-index: 2;
        animation: pulse 2s infinite;
    }

    .image-status.has-image {
        background: #4CAF50;
    }

    .image-status.no-image {
        background: #ff9800;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Loading animation */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container {
            margin: 100px auto 20px;
            padding: 0 10px;
        }

        .profile-content {
            padding: 20px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
            align-items: center;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .header-container {
            flex-direction: column;
            gap: 1rem;
            padding: 0 1rem;
        }

        .nav {
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-item {
            display: block;
            margin: 10px 0;
        }

        .profile-name {
            font-size: 1.5rem;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Error message styles */
    .error-message {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin: 20px 0;
        text-align: center;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    /* Success message styles */
    .success-message {
        background: linear-gradient(135deg, #51cf66, #40c057);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin: 20px 0;
        text-align: center;
        animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <header class="header">
        <script src="google-translate-widget.js"></script>
        <div class="header-container">
            <div class="logo">JS Weby</div>
            <nav>
                <ul class="nav-menu ">
                    <li><a href="home.php"> Home</a></li>
                    <li><a
                            href="E-Commerce-main/contact.php?id=<?php echo (isset( $_SESSION['customer_name']))? $_SESSION['id']: 'unknown';?>">Contact</a>
                    </li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="userDashboardProfile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 'success'): ?>
        <div class="success-message">
            <i class="fa fa-check-circle"></i> Profile updated successfully!
        </div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if ($profileImageSrc): ?>
                    <img src="<?php echo htmlspecialchars($profileImageSrc); ?>" alt="Profile Image"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="default-icon" style="display: none;">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="image-status has-image" title="Profile image uploaded"></div>
                    <?php else: ?>
                    <div class="default-icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="image-status no-image" title="No profile image"></div>
                    <?php endif; ?>
                </div>
                <div class="profile-name">
                    <?php echo htmlspecialchars($row["full_name"] ?? $row["f_name"] . " " . $row["l_name"] ?? 'User'); ?>
                </div>
                <div class="profile-username">@<?php echo htmlspecialchars($row["username"] ?? 'user'); ?></div>
            </div>

            <div class="profile-content">
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fa fa-user"></i> Personal Information
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">
                                <?php 
                                $fullName = $row["full_name"] ?? ($row["f_name"] . " " . $row["l_name"]);
                                echo htmlspecialchars($fullName ?: 'Not provided'); 
                                ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($row["username"] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($row["email"] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value"><?php echo htmlspecialchars($row["phone"] ?? 'Not provided'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fa fa-map-marker"></i> Address Information
                    </h3>
                    <div class="address-section">
                        <div class="info-value">
                            <?php 
                            $address_parts = array();
                            if (!empty($row["address_line1"])) $address_parts[] = $row["address_line1"];
                            if (!empty($row["address_line2"])) $address_parts[] = $row["address_line2"];
                            if (!empty($row["city"])) $address_parts[] = $row["city"];
                            if (!empty($row["state"])) $address_parts[] = $row["state"];
                            if (!empty($row["country"])) $address_parts[] = $row["country"];
                            if (!empty($row["postal_code"])) $address_parts[] = $row["postal_code"];
                            
                            if (!empty($address_parts)) {
                                echo htmlspecialchars(implode(", ", $address_parts));
                            } else {
                                echo "📍 Address not provided";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fa fa-info-circle"></i> Account Details
                    </h3>
                    <div class="stats-section">
                        <div class="stat-item">
                            <span class="stat-value">
                                <?php 
                                $memberSince = 'N/A';
                                if (isset($row["date"]) && !empty($row["date"])) {
                                    $memberSince = date('M d, Y', strtotime($row["date"]));
                                } elseif (isset($row["reg_date"]) && !empty($row["reg_date"])) {
                                    $memberSince = date('M d, Y', strtotime($row["reg_date"]));
                                } elseif (isset($row["created_at"]) && !empty($row["created_at"])) {
                                    $memberSince = date('M d, Y', strtotime($row["created_at"]));
                                }
                                echo $memberSince;
                                ?>
                            </span>
                            <span class="stat-label">Member Since</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">
                                <?php echo isset($row["status"]) ? ucfirst(htmlspecialchars($row["status"])) : 'Active'; ?>
                            </span>
                            <span class="stat-label">Account Status</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">
                                <?php echo $profileImageSrc ? 'Yes' : 'No'; ?>
                            </span>
                            <span class="stat-label">Profile Image</span>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
    // Enhanced interactive effects
    document.addEventListener('DOMContentLoaded', function() {
        // Add enhanced hover effects to info items
        const infoItems = document.querySelectorAll('.info-item');
        infoItems.forEach((item, index) => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.03)';
                this.style.zIndex = '10';
            });

            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.zIndex = '1';
            });

            // Staggered animation on load
            setTimeout(() => {
                item.style.opacity = '1';
            }, index * 100);
        });

        // Profile avatar click effect
        const profileAvatar = document.querySelector('.profile-avatar');
        if (profileAvatar) {
            profileAvatar.addEventListener('click', function() {
                this.style.transform = 'scale(1.1) rotate(5deg)';
                setTimeout(() => {
                    this.style.transform = 'scale(1) rotate(0deg)';
                }, 200);
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Enhanced image error handling
        const profileImage = document.querySelector('.profile-avatar img');
        if (profileImage) {
            profileImage.addEventListener('error', function() {
                console.log('Profile image failed to load');
                this.style.display = 'none';
                const defaultIcon = this.nextElementSibling;
                if (defaultIcon) {
                    defaultIcon.style.display = 'flex';
                }

                // Update image status indicator
                const statusIndicator = document.querySelector('.image-status');
                if (statusIndicator) {
                    statusIndicator.classList.remove('has-image');
                    statusIndicator.classList.add('no-image');
                    statusIndicator.title = 'Profile image failed to load';
                }
            });

            profileImage.addEventListener('load', function() {
                console.log('Profile image loaded successfully');
            });
        }

        // Add loading effect to buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.href && !this.href.includes('#')) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<div class="loading"></div> Loading...';
                    this.style.pointerEvents = 'none';

                    // Reset after 3 seconds if page doesn't change
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }, 3000);
                }
            });
        });

        // Add typing effect to profile name
        const profileName = document.querySelector('.profile-name');
        if (profileName) {
            const text = profileName.textContent;
            profileName.textContent = '';
            let i = 0;

            function typeWriter() {
                if (i < text.length) {
                    profileName.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 100);
                }
            }

            setTimeout(typeWriter, 1000);
        }

        // Auto-hide success/error messages
        const messages = document.querySelectorAll('.success-message, .error-message');
        messages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    message.remove();
                }, 300);
            }, 5000);
        });
    });

    // Add some fun easter eggs
    let clickCount = 0;
    document.querySelector('.logo').addEventListener('click', function() {
        clickCount++;
        if (clickCount === 5) {
            this.style.animation = 'spin 1s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
                clickCount = 0;
            }, 1000);
        }
    });
    </script>
    <script>
    (function() {
        if (!window.chatbase || window.chatbase("getState") !== "initialized") {
            window.chatbase = (...arguments) => {
                if (!window.chatbase.q) {
                    window.chatbase.q = [];
                }
                window.chatbase.q.push(arguments);
            };
            window.chatbase = new Proxy(window.chatbase, {
                get(target, prop) {
                    if (prop === "q") {
                        return target.q;
                    }
                    return (...args) => target(prop, ...args);
                },
            });
        }
        const onLoad = function() {
            const script = document.createElement("script");
            script.src = "https://www.chatbase.co/embed.min.js";
            script.id = "s6OI0Na4jrOMIUgze0iZ7";
            script.domain = "www.chatbase.co";
            document.body.appendChild(script);
        };
        if (document.readyState === "complete") {
            onLoad();
        } else {
            window.addEventListener("load", onLoad);
        }
    })();
    </script>
</body>

</html>

<?php
// Close prepared statement and database connection
$stmt->close();
$conn->close();
?>