<?php
include "config.php";

if (isset($_GET['id'])) {
    $u_id = intval($_GET['id']); // Sanitize

    $sql = "DELETE FROM users WHERE u_id = $u_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
