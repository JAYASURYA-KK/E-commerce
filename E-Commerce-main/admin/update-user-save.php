<?php
include "config.php";

if (isset($_POST['update'])) {
    $u_id = intval($_POST['u_id']);
    $username = $_POST['username'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE users SET 
        username = '$username',
        f_name = '$f_name',
        l_name = '$l_name',
        email = '$email',
        phone = '$phone',
        address = '$address'
        WHERE u_id = $u_id";

    if ($conn->query($sql) === TRUE) {
        echo "User updated successfully.";
        header("Location: users.php");
    } else {
        echo "Error updating user: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
