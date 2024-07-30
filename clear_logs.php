<?php
// clear_logs.php

// Include database connection
include 'db_connection.php';

// Delete all user activity logs
$sql = "DELETE FROM user_activity";

if (mysqli_query($conn, $sql)) {
    // Logs cleared successfully
    echo "Logs cleared successfully.";
} else {
    // Error clearing logs
    echo "Error: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
