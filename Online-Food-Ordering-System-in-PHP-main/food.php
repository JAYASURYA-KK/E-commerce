<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();

// Get search parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($db, $_GET['category']) : '';
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Food Menu || Online Food Ordering System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
    /* Improved Flex Layout */
    .food-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
        align-items: stretch;
    }

    .food-item-wrapper {
        flex: 1 1 300px;
        /* Grow, shrink, basis */
        min-width: 300px;
        max-width: 400px;
        display: flex;
    }

    .food-card {
        border: 1px solid #ddd;
        border-radius: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        background: white;
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
    }

    .food-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .food-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .food-card:hover .food-image {
        transform: scale(1.05);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 220px;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
    }

    .food-card:hover .image-overlay {
        opacity: 1;
    }

    .overlay-text {
        color: white;
        text-align: center;
        font-weight: bold;
    }

    .search-container {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        padding: 50px 0;
        margin-bottom: 40px;
    }

    .search-form {
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 18px 25px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        outline: none;
    }

    .search-btn {
        background: #28a745;
        border: none;
        border-radius: 50px;
        padding: 18px 30px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        white-space: nowrap;
    }

    .search-btn:hover {
        background: #218838;
        transform: translateY(-2px);
    }

    .voice-btn {
        background: #dc3545;
        border: none;
        border-radius: 50%;
        width: 55px;
        height: 55px;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .voice-btn:hover {
        background: #c82333;
        transform: scale(1.05);
    }

    .voice-btn.listening {
        background: #28a745;
        animation: pulse 1.5s infinite;
    }

    .voice-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
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

    .food-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .food-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
        line-height: 1.3;
    }

    .food-restaurant {
        color: #666;
        font-size: 14px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .food-price {
        font-size: 22px;
        font-weight: bold;
        color: #ff6b35;
        margin-bottom: 10px;
    }

    .food-description {
        color: #777;
        font-size: 14px;
        line-height: 1.4;
        flex-grow: 1;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-view-menu,
    .btn-view-restaurant {
        flex: 1;
        text-align: center;
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-view-menu {
        background: #28a745;
        color: white;
    }

    .btn-view-menu:hover {
        background: #218838;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .btn-view-restaurant {
        background: #17a2b8;
        color: white;
    }

    .btn-view-restaurant:hover {
        background: #138496;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .no-results {
        text-align: center;
        padding: 60px;
        color: #666;
    }

    .filter-tabs {
        margin-bottom: 40px;
        text-align: center;
    }

    .filter-tab {
        background: #f8f9fa;
        border: 2px solid #ddd;
        padding: 12px 25px;
        margin: 5px;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-block;
        font-weight: 500;
        text-decoration: none;
        color: #333;
    }

    .filter-tab.active,
    .filter-tab:hover {
        background: #ff6b35;
        color: white;
        border-color: #ff6b35;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .category-select {
        min-width: 180px;
        padding: 18px 20px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        outline: none;
        background: white;
    }

    .image-error {
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 220px;
        color: #6c757d;
        flex-direction: column;
    }

    .category-badge {
        background: #17a2b8;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        margin-left: 10px;
    }

    .results-info {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        text-align: center;
    }

    .clear-search {
        background: #6c757d;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        margin-left: 10px;
    }

    .clear-search:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }

    .voice-status {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 30px;
        border-radius: 15px;
        z-index: 9999;
        display: none;
        text-align: center;
        min-width: 300px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .search-btn.loading {
        background: #6c757d;
        pointer-events: none;
    }

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .food-container {
            gap: 15px;
        }

        .food-item-wrapper {
            flex: 1 1 280px;
            min-width: 280px;
        }

        .search-form {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input,
        .category-select {
            min-width: auto;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .food-item-wrapper {
            flex: 1 1 100%;
            min-width: 100%;
        }
    }
    </style>
</head>

<body>
    <header id="header" class="header-scroll top-header headrom">
        <script src="../google-translate-widget.js"></script>
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse"
                    data-target="#mainNavbarCollapse">&#9776;</button>
                <a class="navbar-brand" href="index.php">JS Weby</a>
                <a class="navbar-brand" href="index.php"> <img class="img-rounded" src="images/logo.png" alt=""
                        width="18%"> </a>
                <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span
                                    class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span
                                    class="sr-only"></span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="food.php">Food Menu <span
                                    class="sr-only"></span></a> </li>

                        <?php
                        if(empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                              <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                        } else {
                            echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                            echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Voice Status Modal -->
    <div id="voiceStatus" class="voice-status">
        <div class="text-center">
            <i class="fa fa-microphone fa-3x mb-3" style="color: #28a745;"></i>
            <h4>🎤 Listening...</h4>
            <p>Speak clearly into your microphone</p>
            <small style="opacity: 0.8;">Try saying: "biryani", "pizza", "chicken", "dosa"</small>
            <br><br>
            <button onclick="stopVoiceSearch()"
                style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer;">
                <i class="fa fa-times"></i> Cancel
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <!-- Search Section -->
        <div class="search-container">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center text-white mb-4">🍛 Find Your Favorite Food 🍛</h1>
                        <p class="text-center text-white mb-4">Click on food images to view restaurant menu</p>
                        <form method="GET" action="food.php" class="search-form" id="searchForm">
                            <input type="text" name="search" class="search-input"
                                placeholder="Search for dishes, restaurants, or cuisine..."
                                value="<?php echo htmlspecialchars($search); ?>" id="searchInput">

                            <select name="category" class="category-select" id="categorySelect">
                                <option value="">All Categories</option>
                                <?php
                                // Get actual categories from database
                                $cat_query = "SELECT DISTINCT r.c_id, 
                                             CASE 
                                                WHEN r.c_id = 1 THEN 'South Indian'
                                                WHEN r.c_id = 2 THEN 'North Indian' 
                                                WHEN r.c_id = 3 THEN 'Chinese'
                                                WHEN r.c_id = 4 THEN 'Continental'
                                                ELSE 'Other'
                                             END as category_name
                                             FROM restaurant r 
                                             INNER JOIN dishes d ON r.rs_id = d.rs_id
                                             ORDER BY r.c_id";
                                $cat_result = mysqli_query($db, $cat_query);
                                while($cat_row = mysqli_fetch_array($cat_result)) {
                                    $selected = ($category == $cat_row['c_id']) ? 'selected' : '';
                                    echo "<option value='".$cat_row['c_id']."' $selected>".$cat_row['category_name']."</option>";
                                }
                                ?>
                            </select>

                            <button type="submit" class="search-btn" id="searchButton">
                                <i class="fa fa-search"></i> Search
                            </button>

                            <button type="button" id="voiceBtn" class="voice-btn" title="Voice Search">
                                <i class="fa fa-microphone"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="container">
            <div class="filter-tabs">
                <a href="food.php"
                    class="filter-tab <?php echo empty($search) && empty($category) ? 'active' : ''; ?>">All Foods</a>
                <a href="food.php?search=biryani"
                    class="filter-tab <?php echo $search == 'biryani' ? 'active' : ''; ?>">Biryani</a>
                <a href="food.php?search=dosa"
                    class="filter-tab <?php echo $search == 'dosa' ? 'active' : ''; ?>">Dosa</a>
                <a href="food.php?search=chicken"
                    class="filter-tab <?php echo $search == 'chicken' ? 'active' : ''; ?>">Chicken</a>
                <a href="food.php?category=1" class="filter-tab <?php echo $category == '1' ? 'active' : ''; ?>">South
                    Indian</a>
                <a href="food.php?category=2" class="filter-tab <?php echo $category == '2' ? 'active' : ''; ?>">North
                    Indian</a>
                <a href="food.php?category=3"
                    class="filter-tab <?php echo $category == '3' ? 'active' : ''; ?>">Chinese</a>
            </div>
        </div>

        <!-- Food Items Section -->
        <section class="food-section">
            <div class="container">
                <?php
                // Improved query for "All Foods" - shows all food items from all categories
                $query = "SELECT d.*, r.title as restaurant_name, r.rs_id, r.c_id as category_id, r.address as restaurant_address
                         FROM dishes d 
                         JOIN restaurant r ON d.rs_id = r.rs_id";
                
                $conditions = [];
                
                // Add search condition - improved search to include more fields
                if (!empty($search)) {
                    $searchTerm = strtolower(trim($search));
                    $conditions[] = "(LOWER(d.title) LIKE '%$searchTerm%' OR LOWER(d.slogan) LIKE '%$searchTerm%' OR LOWER(r.title) LIKE '%$searchTerm%')";
                }
                
                // Add category condition - only when specifically selected
                if (!empty($category) && $category !== 'all') {
                    $conditions[] = "r.c_id = '$category'";
                }
                
                if (!empty($conditions)) {
                    $query .= " WHERE " . implode(" AND ", $conditions);
                }
                
                $query .= " ORDER BY d.d_id DESC";
                
                $result = mysqli_query($db, $query);
                $total_results = mysqli_num_rows($result);
                
                // Show search results info
                if (!empty($search) || !empty($category)) {
                    $search_text = !empty($search) ? "\"$search\"" : "";
                    $category_names = [1 => 'South Indian', 2 => 'North Indian', 3 => 'Chinese', 4 => 'Continental'];
                    $category_text = !empty($category) ? "in " . $category_names[$category] . " category" : "";
                    echo "<div class='results-info'>
                            <strong>$total_results</strong> food items found for $search_text $category_text
                            <a href='food.php' class='clear-search'><i class='fa fa-times'></i> Clear Search</a>
                          </div>";
                } else {
                    echo "<div class='results-info'>
                            <strong>$total_results</strong> delicious food items from all categories
                          </div>";
                }
                ?>

                <div class="food-container" id="foodContainer">
                    <?php 
                    if ($total_results > 0) {
                        while($row = mysqli_fetch_array($result)) {
                            // Function to get image path
                            function getImagePath($imageName) {
                                if (empty($imageName)) {
                                    return 'https://via.placeholder.com/400x300/ff6b35/ffffff?text=No+Image';
                                }
                                
                                $imagePath = 'admin/Res_img/dishes/' . $imageName;
                                
                                if (file_exists($imagePath)) {
                                    return $imagePath;
                                } else {
                                    return 'https://via.placeholder.com/400x300/ff6b35/ffffff?text=' . urlencode('Food Image');
                                }
                            }
                            
                            $image_path = getImagePath($row['img']);
                            
                            // Get category name
                            $category_names = [
                                1 => 'South Indian',
                                2 => 'North Indian', 
                                3 => 'Chinese',
                                4 => 'Continental'
                            ];
                            $category_name = isset($category_names[$row['category_id']]) ? $category_names[$row['category_id']] : 'Other';
                            
                            echo '
                            <div class="food-item-wrapper">
                                <div class="food-card">
                                    <div class="image-container" onclick="goToDishes('.$row['rs_id'].')">
                                        <img src="'.$image_path.'" alt="'.$row['title'].'" class="food-image" 
                                             onerror="this.parentElement.innerHTML=\'<div class=\\\'image-error\\\'><i class=\\\'fa fa-image fa-3x mb-2\\\'></i><p>Image not available</p></div>\'">
                                        <div class="image-overlay">
                                            <div class="overlay-text">
                                                <i class="fa fa-cutlery fa-2x mb-2"></i><br>
                                                <span>View Restaurant Menu</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="food-info">
                                        <div class="food-title">'.$row['title'].'</div>
                                        <div class="food-restaurant">
                                            <i class="fa fa-building"></i> '.$row['restaurant_name'].'
                                            <span class="category-badge">'.$category_name.'</span>
                                        </div>
                                        <div class="food-restaurant">
                                            <i class="fa fa-map-marker"></i> '.substr($row['restaurant_address'], 0, 50).'...
                                        </div>
                                        <div class="food-price">₹'.$row['price'].'</div>
                                        <div class="food-description">'.substr($row['slogan'], 0, 90).'...</div>
                                        
                                        <div class="action-buttons">
                                            <a href="dishes.php?res_id='.$row['rs_id'].'" class="btn-view-menu">
                                                <i class="fa fa-cutlery"></i> View Menu
                                            </a>
                                            <a href="restaurants.php?restaurant_id='.$row['rs_id'].'" class="btn-view-restaurant">
                                                <i class="fa fa-building"></i> Restaurant
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<div class="no-results">
                                <i class="fa fa-search fa-4x mb-4"></i>
                                <h3>No food items found</h3>
                                <p>Try searching with different keywords or browse all categories</p>
                                <a href="food.php" class="btn btn-primary mt-3">View All Foods</a>
                              </div>';
                    }
                    ?>
                </div>

                <!-- Show total count -->
                <?php if ($total_results > 0): ?>
                <div class="text-center mt-4">
                    <p class="text-muted">
                        Showing <?php echo $total_results; ?> delicious food items
                        <?php if (!empty($category)): ?>
                        from <?php echo $category_names[$category]; ?> restaurants
                        <?php endif; ?>
                    </p>
                    <small class="text-info">
                        <i class="fa fa-info-circle"></i> Click on food images to view restaurant menu | Use buttons to
                        navigate
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include "include/footer.php" ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>

    <script>
    // Fixed Voice Search Functionality
    let recognition = null;
    let isListening = false;
    let voiceSupported = false;

    // Initialize speech recognition with better browser support
    function initSpeechRecognition() {
        try {
            // Check for browser support
            if ('webkitSpeechRecognition' in window) {
                recognition = new webkitSpeechRecognition();
                voiceSupported = true;
            } else if ('SpeechRecognition' in window) {
                recognition = new SpeechRecognition();
                voiceSupported = true;
            } else {
                console.log('Speech recognition not supported in this browser');
                document.getElementById('voiceBtn').style.display = 'none';
                return false;
            }

            // Configure recognition with better settings
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
            recognition.maxAlternatives = 3;

            // Event handlers with improved error handling
            recognition.onstart = function() {
                console.log('🎤 Voice recognition started');
                isListening = true;
                document.getElementById('voiceBtn').classList.add('listening');
                document.getElementById('voiceBtn').disabled = true;
                document.getElementById('voiceStatus').style.display = 'block';
                document.getElementById('searchInput').placeholder = '🎤 Listening...';
            };

            recognition.onresult = function(event) {
                console.log('🎯 Voice recognition result received');

                let transcript = '';
                let confidence = 0;

                // Get the best result
                for (let i = 0; i < event.results.length; i++) {
                    if (event.results[i].isFinal) {
                        transcript = event.results[i][0].transcript.trim();
                        confidence = event.results[i][0].confidence;
                        break;
                    }
                }

                console.log('Transcript:', transcript);
                console.log('Confidence:', confidence);

                if (transcript && transcript.length > 0) {
                    // Clean up the transcript
                    transcript = transcript.toLowerCase().replace(/[^\w\s]/gi, '');

                    // Set the transcript in search input
                    document.getElementById('searchInput').value = transcript;

                    // Hide status modal
                    document.getElementById('voiceStatus').style.display = 'none';

                    // Auto-submit the form after a short delay
                    setTimeout(function() {
                        document.getElementById('searchForm').submit();
                    }, 500);
                } else {
                    alert('No speech detected. Please try again.');
                }
            };

            recognition.onend = function() {
                console.log('🛑 Voice recognition ended');
                isListening = false;
                document.getElementById('voiceBtn').classList.remove('listening');
                document.getElementById('voiceBtn').disabled = false;
                document.getElementById('voiceStatus').style.display = 'none';
                document.getElementById('searchInput').placeholder =
                    'Search for dishes, restaurants, or cuisine...';
            };

            recognition.onerror = function(event) {
                console.error('❌ Speech recognition error:', event.error);
                isListening = false;
                document.getElementById('voiceBtn').classList.remove('listening');
                document.getElementById('voiceBtn').disabled = false;
                document.getElementById('voiceStatus').style.display = 'none';
                document.getElementById('searchInput').placeholder =
                    'Search for dishes, restaurants, or cuisine...';

                // Show user-friendly error messages
                let errorMessage = 'Voice search error: ';
                switch (event.error) {
                    case 'no-speech':
                        errorMessage += 'No speech detected. Please speak clearly and try again.';
                        break;
                    case 'audio-capture':
                        errorMessage += 'Microphone not available. Please check your microphone settings.';
                        break;
                    case 'not-allowed':
                        errorMessage +=
                            'Microphone permission denied. Please allow microphone access and try again.';
                        break;
                    case 'network':
                        errorMessage += 'Network error occurred. Please check your internet connection.';
                        break;
                    case 'aborted':
                        errorMessage = 'Voice search cancelled.';
                        break;
                    default:
                        errorMessage += 'Please try again or use text search.';
                }

                if (event.error !== 'aborted') {
                    alert(errorMessage);
                }
            };

            return true;
        } catch (error) {
            console.error('Error initializing speech recognition:', error);
            document.getElementById('voiceBtn').style.display = 'none';
            return false;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        voiceSupported = initSpeechRecognition();
        console.log('Speech recognition support:', voiceSupported);

        if (!voiceSupported) {
            document.getElementById('voiceBtn').title = 'Voice search not supported in this browser';
            document.getElementById('voiceBtn').disabled = true;
        }
    });

    // Voice button click handler with better error handling
    document.getElementById('voiceBtn').addEventListener('click', function() {
        if (!voiceSupported || !recognition) {
            alert('Voice search is not supported in your browser. Please use text search.');
            return;
        }

        if (isListening) {
            console.log('🛑 Stopping voice recognition');
            recognition.stop();
        } else {
            console.log('🎤 Starting voice recognition');
            try {
                // Request microphone permission first
                navigator.mediaDevices.getUserMedia({
                        audio: true
                    })
                    .then(function(stream) {
                        // Stop the stream immediately, we just needed permission
                        stream.getTracks().forEach(track => track.stop());

                        // Now start recognition
                        recognition.start();
                    })
                    .catch(function(error) {
                        console.error('Microphone permission error:', error);
                        alert(
                            'Microphone access is required for voice search. Please allow microphone access and try again.'
                            );
                    });
            } catch (error) {
                console.error('Error starting recognition:', error);
                alert('Could not start voice recognition. Please try again or use text search.');
            }
        }
    });

    // Function to stop voice search
    function stopVoiceSearch() {
        if (recognition && isListening) {
            recognition.stop();
        }
    }

    // Navigate to dishes.php with correct restaurant ID
    function goToDishes(restaurantId) {
        console.log('Navigating to dishes page for restaurant:', restaurantId);
        window.location.href = 'dishes.php?res_id=' + restaurantId;
    }

    // Navigate to restaurants.php
    function goToRestaurant(restaurantId) {
        console.log('Navigating to restaurant page for restaurant:', restaurantId);
        window.location.href = 'restaurants.php?restaurant_id=' + restaurantId;
    }

    // Enhanced search form submission with loading animation
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchButton');

        // Trim whitespace from search input
        searchInput.value = searchInput.value.trim();

        // Show loading animation
        searchBtn.classList.add('loading');
        searchBtn.innerHTML = '<span class="loading-spinner"></span> Searching...';
        searchBtn.disabled = true;

        console.log('🔍 Searching for:', searchInput.value);
    });

    // Auto-submit form when category changes
    document.getElementById('categorySelect').addEventListener('change', function() {
        console.log('📂 Category changed to:', this.value);
        document.getElementById('searchForm').submit();
    });

    // Enhanced Enter key search
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.value = this.value.trim();
            document.getElementById('searchForm').submit();
        }
    });

    // Close voice status modal on outside click
    document.getElementById('voiceStatus').addEventListener('click', function(e) {
        if (e.target === this) {
            stopVoiceSearch();
        }
    });

    // Add keyboard support for voice status modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('voiceStatus').style.display === 'block') {
            stopVoiceSearch();
        }
    });

    // Reset loading state if page is loaded via back/forward navigation
    window.addEventListener('pageshow', function(event) {
        const searchBtn = document.getElementById('searchButton');
        if (searchBtn.classList.contains('loading')) {
            searchBtn.classList.remove('loading');
            searchBtn.innerHTML = '<i class="fa fa-search"></i> Search';
            searchBtn.disabled = false;
        }
    });

    // Add loading state management
    window.addEventListener('beforeunload', function() {
        const searchBtn = document.getElementById('searchButton');
        if (searchBtn.classList.contains('loading')) {
            searchBtn.classList.remove('loading');
            searchBtn.innerHTML = '<i class="fa fa-search"></i> Search';
            searchBtn.disabled = false;
        }
    });

    // Debug information
    console.log('🍛 Food Menu Page Loaded');
    console.log('🎤 Speech Recognition Available:', voiceSupported);
    console.log('🔍 Current Search:', '<?php echo addslashes($search); ?>');
    console.log('📂 Current Category:', '<?php echo addslashes($category); ?>');
    </script>
</body>

</html>