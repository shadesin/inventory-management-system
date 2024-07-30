<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include log_activity function
include 'log_activity.php';

// Process log activity only when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Example of logging activity
    $user_id = $_SESSION["user_id"]; // Assuming you store user ID in session
    $activity = "Added an order";
    log_activity($user_id, $activity);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your custom styles here */
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Order</h2>
        <form action="process_order.php" method="post">
            <label for="status">Order Status:</label>
            <select id="status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="Delivered">Delivered</option>
            </select>
            <label for="subtotal">Subtotal:</label>
            <input type="number" id="subtotal" name="subtotal" step="0.01" required>
            <label for="shipping">Shipping:</label>
            <input type="number" id="shipping" name="shipping" step="0.01" required>
            <button type="submit">Add Order</button>
        </form>
    </div>
</body>
</html>
