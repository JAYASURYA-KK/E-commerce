<?php
// Start session to access user data
session_start();

// Initialize variables
$isLoggedIn = false;
$userData = null;
$profileImageSrc = null;


// Assume username is stored in session as 'username'

// FIXED: Get username from session, don't overwrite it
$sessionUsername = $_SESSION['username'] ?? 'User';

$playVoice = false;
if (isset($_SESSION['play_sound']) && $_SESSION['play_sound']) {
    $playVoice = true;
    unset($_SESSION['play_sound']);
}

// Only check for user if session exists and is valid
if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "onlinefoodphp";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection and get user data
    if (!$conn->connect_error) {
        try {
            $user_id = (int)$_SESSION["user_id"];
            $stmt = $conn->prepare("SELECT u_id, username, full_name, f_name, l_name, user_image, image_mime_type FROM users WHERE u_id = ?");
            
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $userData = $result->fetch_assoc();
                    $isLoggedIn = true;
                } else {
                    // User not found - silently clear session and continue as guest
                    session_unset();
                    session_destroy();
                    session_start(); // Start fresh session
                    $isLoggedIn = false;
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            // Database error - continue as guest
            $isLoggedIn = false;
        }
        
        $conn->close();
    }
}

// Function to display profile image
function getProfileImageSrc($userData) {
    if (!$userData || empty($userData['user_image'])) {
        return null;
    }
    
    // Check if it's base64 encoded
    if (base64_encode(base64_decode($userData['user_image'], true)) === $userData['user_image']) {
        $mimeType = $userData['image_mime_type'] ?: 'image/jpeg';
        return "data:$mimeType;base64," . $userData['user_image'];
    }
    
    // If it's a file path, check if file exists
    if (is_string($userData['user_image']) && file_exists($userData['user_image'])) {
        return $userData['user_image'];
    }
    
    return null;
}

// Get profile image if user is logged in
if ($isLoggedIn && $userData) {
    $profileImageSrc = getProfileImageSrc($userData);
}

// Get user display name
function getUserDisplayName($userData) {
    if (!$userData) return 'Guest';
    
    if (!empty($userData['full_name'])) {
        return $userData['full_name'];
    } elseif (!empty($userData['f_name']) && !empty($userData['l_name'])) {
        return $userData['f_name'] . ' ' . $userData['l_name'];
    } elseif (!empty($userData['username'])) {
        return $userData['username'];
    }
    
    return 'User';
}

$userDisplayName = getUserDisplayName($userData);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JS Weby - Your Ultimate Shopping Destination</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Arial", sans-serif;
        line-height: 1.6;
        color: #333;
        overflow-x: hidden;
    }

    /* Header Styles */
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
        color: white;
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

    .auth-buttons {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-login {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-login:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }

    .btn-signup {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
    }

    .btn-signup:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(238, 90, 36, 0.4);
    }

    /* User profile styles */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .user-profile:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }

    .user-avatar:hover {
        transform: scale(1.1);
        border-color: white;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-avatar .default-icon {
        color: white;
        font-size: 1.5rem;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.9rem;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-action {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 180px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 5px;
    }

    .dropdown:hover .dropdown-content {
        display: block;
        animation: fadeInDown 0.3s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-content a {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .dropdown-content a:last-child {
        border-bottom: none;
    }

    .dropdown-content a:hover {
        background-color: #f8f9fa;
        color: #667eea;
        padding-left: 20px;
    }

    .dropdown-content a i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 120px 0 80px;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 1;
    }

    .hero h1 {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        animation: fadeInUp 1s ease;
    }

    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        animation: fadeInUp 1s ease 0.2s both;
    }

    .cta-button {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        border-radius: 50px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        animation: fadeInUp 1s ease 0.4s both;
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(238, 90, 36, 0.4);
    }

    /* Categories Section */
    .categories {
        padding: 80px 0;
        background: #f8f9fa;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: #333;
        position: relative;
    }

    .section-title::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .category-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .category-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .category-card h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .category-card p {
        color: #666;
        font-size: 0.9rem;
    }

    /* Footer */
    .footer {
        background: #2c3e50;
        color: white;
        padding: 50px 0 20px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-bottom: 2rem;
    }

    .footer-section h3 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        color: #fff;
    }

    .footer-section p,
    .footer-section a {
        color: #bdc3c7;
        text-decoration: none;
        line-height: 1.8;
    }

    .footer-section a:hover {
        color: #667eea;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid #34495e;
        color: #95a5a6;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }

        .hero h1 {
            font-size: 2.5rem;
        }

        .header-container {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .auth-buttons {
            order: 3;
            width: 100%;
            justify-content: center;
        }

        .user-info {
            display: none;
        }

        .dropdown-content {
            right: -50px;
            min-width: 160px;
        }
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <script src="google-translate-widget.js"></script>
        <div class="header-container">
            <div class="logo" onclick="window.location.href='home.php'">JS Weby</div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a
                            href="E-Commerce-main/contact.php?id=<?php echo (isset( $_SESSION['customer_name']))? $_SESSION['id']: 'unknown';?>">Contact</a>
                    </li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="spin.php">spin</a></li>
                    <li><a href="withdraw.php">withdraw</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="userDashboardProfile.php">Profile</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($isLoggedIn && $userData): ?>
                <!-- User is logged in and data found, show profile dropdown -->
                <div class="dropdown">
                    <div class="user-profile">
                        <div class="user-avatar">
                            <?php if ($profileImageSrc): ?>
                            <img src="<?php echo htmlspecialchars($profileImageSrc); ?>" alt="Profile"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="default-icon" style="display: none;">
                                <i class="fa fa-user"></i>
                            </div>
                            <?php else: ?>
                            <div class="default-icon">
                                <i class="fa fa-user"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <span
                                class="user-name"><?php echo htmlspecialchars($userData['username'] ?? 'User'); ?></span>
                            <span class="user-action">Click for options</span>
                        </div>
                    </div>
                    <div class="dropdown-content">
                        <a href="userDashboardProfile.php"><i class="fa fa-user"></i> My Profile</a>

                        <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                    </div>
                </div>
                <?php else: ?>
                <!-- User is not logged in or user ID not found, show login/signup buttons -->
                <a href="Online-Food-Ordering-System-in-PHP-main/login.php" class="btn btn-login">
                    <i class="fa fa-sign-in"></i> Login
                </a>
                <a href="Online-Food-Ordering-System-in-PHP-main/registration.php" class="btn btn-signup">
                    <i class="fa fa-user-plus"></i> Sign Up
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>



    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>
                Welcome to JS Weby
                <?php if ($isLoggedIn && $userData): ?>
                , <?php echo htmlspecialchars($userDisplayName); ?>!
                <?php endif; ?>
            </h1>
            <p>
                Your ultimate destination for food, fashion, electronics, and
                everything in between. Discover amazing deals across multiple stores!
            </p>
            <?php if (!$isLoggedIn): ?>
            <a href="Online-Food-Ordering-System-in-PHP-main/login.php" class="cta-button">
                <i class="fa fa-rocket"></i> Get Started
            </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="category-grid">
                <div class="category-card"
                    onclick="window.location.href='Online-Food-Ordering-System-in-PHP-main/index.php'">
                    <span class="category-icon">🍕</span>
                    <h3>Food & Beverages</h3>
                    <p>Fresh groceries, snacks, and beverages delivered to your door</p>
                </div>
                <div class="category-card" onclick="window.location.href='E-Commerce-main/index.php'">
                    <span class="category-icon">👗</span>
                    <h3>Fashion & Style</h3>
                    <p>Latest trends in clothing, shoes, and accessories</p>
                </div>
                <div class="category-card"
                    onclick="window.location.href='Tourism-Management-System-main/travel/index.php'">
                    <span class="category-icon">✈️</span>
                    <h3>Travel & Tours</h3>
                    <p>Administer destinations, bookings, and travel packages</p>
                </div>
                <div class="category-card" onclick="window.location.href='oops.html'">
                    <span class="category-icon">🏠</span>
                    <h3>Home & Garden</h3>
                    <p>Furniture, decor, kitchen essentials, and garden supplies</p>
                </div>
                <div class="category-card" onclick="window.location.href='oops.html'">
                    <span class="category-icon">💄</span>
                    <h3>Health & Beauty</h3>
                    <p>Skincare, cosmetics, supplements, and wellness products</p>
                </div>
                <div class="category-card" onclick="window.location.href='oops.html'">
                    <span class="category-icon">⚽</span>
                    <h3>Sports & Fitness</h3>
                    <p>Athletic wear, equipment, and fitness accessories</p>
                </div>
                <div class="category-card" onclick="window.location.href='oops.html'">
                    <span class="category-icon">📚</span>
                    <h3>Books & Media</h3>
                    <p>Books, movies, music, and educational materials</p>
                </div>
                <div class="category-card" onclick="window.location.href='oops.html'">
                    <span class="category-icon">🧸</span>
                    <h3>Toys & Games</h3>
                    <p>Fun toys, board games, and educational activities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>JS Weby</h3>
                    <p>
                        Your ultimate shopping destination with access to millions of
                        products from trusted retailers worldwide.
                    </p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="#home">Home</a></p>
                    <p><a href="#categories">Categories</a></p>
                    <p><a href="contact.php">Contact</a></p>
                    <p><a href="about.html">About</a></p>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>📧 jsweby@gmal.com</p>
                    <p>📞 +91 9080418085</p>

                </div>
            </div>
            <div class="footer-bottom">
                <p>

                </p>
            </div>
        </div>
    </footer>

    <script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Add scroll effect to header
    window.addEventListener("scroll", function() {
        const header = document.querySelector(".header");
        if (window.scrollY > 100) {
            header.style.background = "rgba(102, 126, 234, 0.95)";
            header.style.backdropFilter = "blur(10px)";
        } else {
            header.style.background =
                "linear-gradient(135deg, #667eea 0%, #764ba2 100%)";
            header.style.backdropFilter = "none";
        }
    });

    // Category card hover effects
    document.querySelectorAll(".category-card").forEach((card) => {
        card.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-10px) scale(1.02)";
        });

        card.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0) scale(1)";
        });
    });

    // Handle profile image errors
    window.addEventListener("load", function() {
        const profileImage = document.querySelector('.user-avatar img');
        if (profileImage) {
            profileImage.addEventListener('error', function() {
                this.style.display = 'none';
                this.nextElementSibling.style.display = 'block';
            });
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
    <?php if ($playVoice): ?>
    <!-- FIXED: Pass the correct username for voice -->
    <script>
    const username = <?php echo json_encode($userDisplayName); ?>;
    </script>
    <script src="welcome-voice.js"></script>
    <?php endif; ?>
    <script src="smokey-cursor.js"></script>
</body>

</html>