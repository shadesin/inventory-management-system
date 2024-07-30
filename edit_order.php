<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Check if order ID is provided in the URL
if (!isset($_GET["orderid"]) || empty($_GET["orderid"])) {
    header("Location: admin_orders.php");
    exit();
}

$orderid = $_GET["orderid"];

// Fetch order details from the database
$sql = "SELECT * FROM orderdetails WHERE orderid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Order not found
    header("Location: admin_orders.php");
    exit();
}

$order = $result->fetch_assoc();

// Process form submission to update order details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include log_activity function
    include 'log_activity.php';

    // Example of logging activity
    $user_id = $_SESSION["user_id"]; // Assuming you store user ID in session

    // Log when an order is edited
    $activity = "Edited an order";
    log_activity($user_id, $activity);

    // Retrieve form data
    $status = $_POST["status"] ?? $order['status'];
    $subtotal = $_POST["subtotal"] ?? $order['subtotal'];
    $shipping = $_POST["shipping"] ?? $order['shipping'];
    $total = $_POST["total"] ?? $order['total'];
    $updatedat = date("Y-m-d H:i:s"); // Current date and time

    // Update order details in the database
    $sql = "UPDATE orderdetails SET status = ?, subtotal = ?, shipping = ?, total = ?, updatedat = ? 
            WHERE orderid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdddsi", $status, $subtotal, $shipping, $total, $updatedat, $orderid);
    if ($stmt->execute()) {
        // Order updated successfully
        header("Location: admin_orders.php");
        exit();
    } else {
        // Error occurred while updating order
        $error = "Error: Unable to update order. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit Order</h2>
        <form method="post">
            <input type="hidden" name="orderid" value="<?php echo $orderid; ?>">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
            </select><br>
            <label for="subtotal">Subtotal:</label>
            <input type="number" id="subtotal" name="subtotal" value="<?php echo $order['subtotal']; ?>" step="0.01" required><br>
            <label for="shipping">Shipping:</label>
            <input type="number" id="shipping" name="shipping" value="<?php echo $order['shipping']; ?>" step="0.01" required><br>
            <button type="submit">Update Order</button>
        </form>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </div>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
