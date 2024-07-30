<?php
// delete_product.php

// Include log_activity function
include 'log_activity.php';

// Example of logging activity
$user_id = $_SESSION["user_id"]; // Assuming you store user ID in session

// Log when a product is deleted
$activity = "Deleted a product";
log_activity($user_id, $activity);

// Include database connection
include 'db_connection.php';

// Get product ID from request
$pid = $_GET['pid'];

// Delete product from product_category table first
$sql_delete_category = "DELETE FROM product_category WHERE pid='$pid'";
if (mysqli_query($conn, $sql_delete_category)) {
    // Product removed from product_category successfully
    // Now, delete the product from product table
    $sql_delete_product = "DELETE FROM product WHERE pid='$pid'";
    if (mysqli_query($conn, $sql_delete_product)) {
        // Product deleted successfully
        header("Location: admin_inventory.php");
        exit();
    } else {
        // Error deleting product from product table
        echo "Error: " . $sql_delete_product . "<br>" . mysqli_error($conn);
    }
} else {
    // Error deleting product from product_category table
    echo "Error: " . $sql_delete_category . "<br>" . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
