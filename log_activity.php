<?php
// Set the timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// Include database connection
include 'db_connection.php';

// Function to log activity
function log_activity($user_id, $activity) {
    global $conn;

    // Get current timestamp
    $timestamp = date("Y-m-d H:i:s");

    // Prepare and execute SQL statement
    $sql = "INSERT INTO user_activity (user_id, activity, timestamp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $activity, $timestamp);

    if ($stmt->execute()) {
        // Log successfully inserted
        return true;
    } else {
        // Error in logging
        return false;
    }
}
?>
