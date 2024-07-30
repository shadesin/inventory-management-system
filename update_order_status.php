<?php
// Handle form submission to update order status
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST["order_id"];
    $new_status = $_POST["new_status"];

    // Update order status in the database
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();

    // Redirect back to admin orders page
    header("Location: admin_orders.php");
    exit();
}
?>
