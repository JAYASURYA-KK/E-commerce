<?php
include("../connection/connect.php");
session_start();

$message = "";
$error = "";

// Create upload directories
$restaurant_upload_dir = "admin/Res_img/";
$dish_upload_dir = "admin/Res_img/dishes/";

if (!is_dir($restaurant_upload_dir)) {
    mkdir($restaurant_upload_dir, 0755, true);
}
if (!is_dir($dish_upload_dir)) {
    mkdir($dish_upload_dir, 0755, true);
}

// Simple image upload function
function uploadImage($file, $upload_dir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > 5000000) { // 5MB limit
        return false;
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . rand(1000, 9999) . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    }
    
    return false;
}

// Handle Restaurant Form
if (isset($_POST['add_restaurant'])) {
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $url = mysqli_real_escape_string($db, $_POST['url']);
    $o_hr = mysqli_real_escape_string($db, $_POST['o_hr']);
    $c_hr = mysqli_real_escape_string($db, $_POST['c_hr']);
    $o_days = mysqli_real_escape_string($db, $_POST['o_days']);
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $c_id = (int)$_POST['c_id'];
    
    $image_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = uploadImage($_FILES['image'], $restaurant_upload_dir);
        if (!$image_name) {
            $error = "Image upload failed!";
        }
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO restaurant (c_id, title, email, phone, url, o_hr, c_hr, o_days, address, image, date) 
                VALUES ('$c_id', '$title', '$email', '$phone', '$url', '$o_hr', '$c_hr', '$o_days', '$address', '$image_name', NOW())";
        
        if (mysqli_query($db, $sql)) {
            $message = "Restaurant added successfully!";
        } else {
            $error = "Database error: " . mysqli_error($db);
        }
    }
}

// Handle Dish Form
if (isset($_POST['add_dish'])) {
    $rs_id = (int)$_POST['rs_id'];
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $slogan = mysqli_real_escape_string($db, $_POST['slogan']);
    $price = (float)$_POST['price'];
    
    $image_name = "";
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $image_name = uploadImage($_FILES['img'], $dish_upload_dir);
        if (!$image_name) {
            $error = "Image upload failed!";
        }
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO dishes (rs_id, title, slogan, price, img) 
                VALUES ('$rs_id', '$title', '$slogan', '$price', '$image_name')";
        
        if (mysqli_query($db, $sql)) {
            $message = "Dish added successfully!";
        } else {
            $error = "Database error: " . mysqli_error($db);
        }
    }
}

// Handle Tamil Nadu Data Addition
if (isset($_POST['add_tamil_data'])) {
    // First add restaurants
    $restaurants = [
        "INSERT INTO restaurant (c_id, title, email, phone, url, o_hr, c_hr, o_days, address, image, date) VALUES 
        (1, 'Saravana Bhavan', 'info@saravanabhavan.com', '9876543210', 'www.saravanabhavan.com', '6am', '11pm', 'mon-sun', 'T. Nagar, Chennai, Tamil Nadu', 'saravana.jpg', NOW())",
        
        "INSERT INTO restaurant (c_id, title, email, phone, url, o_hr, c_hr, o_days, address, image, date) VALUES 
        (1, 'Murugan Idli Shop', 'info@muruganidli.com', '9876543211', 'www.muruganidli.com', '6am', '10pm', 'mon-sun', 'Chennai, Tamil Nadu', 'murugan.jpg', NOW())",
        
        "INSERT INTO restaurant (c_id, title, email, phone, url, o_hr, c_hr, o_days, address, image, date) VALUES 
        (1, 'Anjappar Restaurant', 'info@anjappar.com', '9876543212', 'www.anjappar.com', '11am', '11pm', 'mon-sun', 'Chennai, Tamil Nadu', 'anjappar.jpg', NOW())"
    ];
    
    foreach ($restaurants as $sql) {
        mysqli_query($db, $sql);
    }
    
    // Then add dishes (using restaurant IDs that were just created)
    $dishes = [
        "INSERT INTO dishes (rs_id, title, slogan, price, img) VALUES 
        (1, 'Chicken Biryani', 'Aromatic basmati rice with tender chicken and spices', 15.99, 'chicken_biryani.jpg')",
        
        "INSERT INTO dishes (rs_id, title, slogan, price, img) VALUES 
        (1, 'Mutton Biryani', 'Fragrant rice with succulent mutton pieces', 18.99, 'mutton_biryani.jpg')",
        
        "INSERT INTO dishes (rs_id, title, slogan, price, img) VALUES 
        (2, 'Masala Dosa', 'Crispy crepe with spiced potato filling', 8.99, 'masala_dosa.jpg')",
        
        "INSERT INTO dishes (rs_id, title, slogan, price, img) VALUES 
        (2, 'Idli Sambar', 'Steamed rice cakes with lentil curry', 6.99, 'idli_sambar.jpg')",
        
        "INSERT INTO dishes (rs_id, title, slogan, price, img) VALUES 
        (3, 'Chettinad Chicken', 'Spicy chicken curry with traditional spices', 14.99, 'chettinad_chicken.jpg')"
    ];
    
    foreach ($dishes as $sql) {
        mysqli_query($db, $sql);
    }
    
    $message = "Tamil Nadu restaurants and dishes added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Content - Restaurant & Dishes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 30px;
    }

    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .btn-primary {
        background-color: #ff6b35;
        border-color: #ff6b35;
    }

    .btn-primary:hover {
        background-color: #e55a2b;
        border-color: #e55a2b;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">🍛 Add Restaurants & Dishes</h1>

        <?php if($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Quick Tamil Nadu Data Addition -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4>🌶️ Quick Add Tamil Nadu Data</h4>
            </div>
            <div class="card-body text-center">
                <p>Add sample Tamil Nadu restaurants and dishes with one click!</p>
                <form method="post" style="display: inline;">
                    <button type="submit" name="add_tamil_data" class="btn btn-info btn-lg"
                        onclick="return confirm('Add Tamil Nadu sample data?')">
                        Add Tamil Nadu Data
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Add Restaurant -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>🏪 Add Restaurant</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Restaurant Name *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select name="c_id" class="form-control" required>
                                    <option value="1">South Indian</option>
                                    <option value="2">North Indian</option>
                                    <option value="3">Chinese</option>
                                    <option value="4">Continental</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" name="url" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Opening Hour *</label>
                                        <input type="text" name="o_hr" class="form-control" placeholder="6am" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Closing Hour *</label>
                                        <input type="text" name="c_hr" class="form-control" placeholder="11pm" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Operating Days *</label>
                                <input type="text" name="o_days" class="form-control" placeholder="mon-sun" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address *</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Restaurant Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" name="add_restaurant" class="btn btn-primary">Add Restaurant</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Dish -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>🍽️ Add Dish</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Select Restaurant *</label>
                                <select name="rs_id" class="form-control" required>
                                    <option value="">Choose Restaurant</option>
                                    <?php
                                    $restaurants = mysqli_query($db, "SELECT rs_id, title FROM restaurant ORDER BY title");
                                    while($row = mysqli_fetch_array($restaurants)) {
                                        echo "<option value='".$row['rs_id']."'>".$row['title']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dish Name *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea name="slogan" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dish Image</label>
                                <input type="file" name="img" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" name="add_dish" class="btn btn-success">Add Dish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Recent Data -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4>📊 Recent Data</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Recent Restaurants</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($db, "SELECT rs_id, title, phone FROM restaurant ORDER BY rs_id DESC LIMIT 5");
                                    while($row = mysqli_fetch_array($result)) {
                                        echo "<tr><td>".$row['rs_id']."</td><td>".$row['title']."</td><td>".$row['phone']."</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Recent Dishes</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($db, "SELECT d_id, title, price FROM dishes ORDER BY d_id DESC LIMIT 5");
                                    while($row = mysqli_fetch_array($result)) {
                                        echo "<tr><td>".$row['d_id']."</td><td>".$row['title']."</td><td>₹".$row['price']."</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <a href="food.php" class="btn btn-primary me-2">View Food Page</a>
            <a href="restaurants.php" class="btn btn-success">View Restaurants</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>