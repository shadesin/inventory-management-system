<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        h3 {
            color: #666;
            margin-bottom: 15px;
        }
        .actions {
            margin-bottom: 30px;
        }
        .actions h4 {
            color: #333;
            margin-bottom: 10px;
        }
        .actions ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .actions li {
            margin-bottom: 10px;
        }
        .actions a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .actions a:hover {
            background-color: #2980b9;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #ff0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
        <h3>Customer Dashboard</h3>
        <div class="actions">
            <h4>Actions:</h4>
            <ul>
                <li><a href="view_products.php">View Products</a></li>
                <li><a href="order_history.php">Order History</a></li>
            </ul>
        </div>
        <button onclick="location.href='logout.php'" class="logout-btn">Logout</button>
    </div>
</body>
</html>
