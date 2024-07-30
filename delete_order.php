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

// Log when a product is deleted
$activity = "Deleted an order";
log_activity($user_id, $activity);

include 'db_connection.php';

// Check if order ID is provided in the URL
if (!isset($_GET["orderid"]) || empty($_GET["orderid"])) {
    header("Location: admin_orders.php");
    exit();
}

$orderid = $_GET["orderid"];

// Delete order from the database
$sql = "DELETE FROM orderdetails WHERE orderid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderid);
if ($stmt->execute()) {
    // Order deleted successfully
    header("Location: admin_orders.php");
    exit();
} else {
    // Error occurred while deleting order
    $error = "Error: Unable to delete order. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Delete Order</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </div>
</body>
</html>
