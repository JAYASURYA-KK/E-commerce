<?php
// Include this file in your checkout pages

function getUserSpinDiscount($conn, $user_id) {
    $discount_query = "SELECT SUM(prize_value) as total_discount FROM spin_history WHERE user_id = ? AND prize_won = '$10' AND used = 0";
    $stmt = $conn->prepare($discount_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $discount_result = $stmt->get_result();
    return $discount_result->fetch_assoc()['total_discount'] ?? 0;
}

function applySpinDiscount($conn, $user_id, $discount_amount) {
    // Mark the discount as used
    $update_query = "UPDATE spin_history SET used = 1 WHERE user_id = ? AND prize_won = '$10' AND used = 0 AND prize_value = ? LIMIT 1";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("id", $user_id, $discount_amount);
    return $stmt->execute();
}

function calculateDiscountedTotal($original_total, $discount_amount) {
    $discounted_total = $original_total - $discount_amount;
    return max(0, $discounted_total); // Ensure total doesn't go below 0
}
?>