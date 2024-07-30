<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include log_activity function
include 'log_activity.php';

// Example of logging activity
$user_id = $_SESSION["user_id"]; // Assuming you store user ID in session

// Log when a product is added
$activity = "Added a new product";
log_activity($user_id, $activity);

// Include database connection
include 'db_connection.php';

// Get form data
$title = $_POST['title'];
$summary = $_POST['summary'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];
$category = $_POST['category'];

// Insert new product into database
$sql = "INSERT INTO product (title, summary, price, createdat) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssd", $title, $summary, $price);

if ($stmt->execute()) {
    // Get the ID of the last inserted product
    $last_id = $conn->insert_id;

    // Insert the product category into product_category table
    $sql = "INSERT INTO product_category (pid, cid) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $last_id, $category);

    if ($stmt->execute()) {
        // Insert the product quantity into inventory table
        $sql = "INSERT INTO inventory (pid, quantity, last_updated) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $last_id, $quantity);

        if ($stmt->execute()) {
            // Product and inventory added successfully
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // Error adding product to inventory
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Error adding product category
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // Error adding product
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
