<?php
include "config.php";

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Sanitize ID

    $sql = "SELECT * FROM users WHERE u_id = $user_id";
    $result = $conn->query($sql);

    if (!$result) {
        die("Query Failed: " . $conn->error); // Show detailed DB error
    }

    $row = $result->fetch_assoc(); // Safe to fetch_assoc now
} else {
    die("Invalid Request: ID not set.");
}
?>

<!-- HTML form to show user data and allow update -->
<h2>Update User</h2>
<form action="update-user-save.php" method="POST">
    <input type="hidden" name="u_id" value="<?php echo $row['u_id']; ?>">
    Username: <input type="text" name="username" value="<?php echo $row['username']; ?>"><br>
    First Name: <input type="text" name="f_name" value="<?php echo $row['f_name']; ?>"><br>
    Last Name: <input type="text" name="l_name" value="<?php echo $row['l_name']; ?>"><br>
    Email: <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
    Phone: <input type="text" name="phone" value="<?php echo $row['phone']; ?>"><br>
    Address: <input type="text" name="address" value="<?php echo $row['address']; ?>"><br>
    <input type="submit" name="update" value="Update">
</form>
