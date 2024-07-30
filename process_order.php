<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'db_connection.php';

    // Retrieve form data
    $status = $_POST["status"];
    $subtotal = $_POST["subtotal"];
    $shipping = $_POST["shipping"];
    $total = $subtotal + $shipping;

    // Get the current datetime
    $currentDateTime = date("Y-m-d H:i:s");

    // Assuming the order is placed by the customer, set the cid to the admin's ID
    // You may need to adjust this part based on your database structure and authentication mechanism
    $cid = $_SESSION["user_id"]; // Assuming user_id represents the admin's ID

    // Insert the order into the database
    $sql = "INSERT INTO orderdetails (cid, status, subtotal, shipping, total, createdat)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isddds", $cid, $status, $subtotal, $shipping, $total, $currentDateTime);
    $stmt->execute();

    // Check if the order was successfully added
    if ($stmt->affected_rows > 0) {
        $message = "Order added successfully!";
    } else {
        $error = "Error adding order: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    mysqli_close($conn);
} else {
    // Redirect to add_order.php if accessed directly without form submission
    header("Location: add_order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Order</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your custom styles here */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .message-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .back-btn {
            margin-top: 20px;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }

        .logout-btn {
            margin-top: 20px;
            background-color: #ff0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($message)) : ?>
            <div class="message-box"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <div class="message-box"><?php echo $error; ?></div>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
