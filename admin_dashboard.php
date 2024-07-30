<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logout-btn {
            background-color: #ff4d4d;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: #ff3333;
        }
        .action-section {
            margin-bottom: 20px;
        }
        .action-section h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .action-btn {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .action-btn:hover {
            background-color: #2980b9;
        }
        .action-btn.alt {
            background-color: #27ae60;
        }
        .action-btn.alt:hover {
            background-color: #219d52;
        }
        .remove-btn {
            background-color: #ff4d4d;
            color: #fff;
            transition: background-color 0.3s;
        }
        .remove-btn:hover {
            background-color: #ff3333;
        }
        .stats-btn {
            background-color: #ff9f43;
            color: #fff;
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .stats-btn:hover {
            background-color: #ff8e38;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome, Admin!</h2>
            <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
        </div>

        <!-- Add New Product Section -->
        <div class="action-section">
            <h3>Add New Product</h3>
            <a href="add_product.php" class="action-btn">Add Product</a>
        </div>

        <!-- Manage Product Categories Section -->
        <div class="action-section">
            <h3>Manage Product Categories</h3>
            <a href="add_category.php" class="action-btn alt">Add Category</a>
            <a href="delete_category.php" class="action-btn remove-btn">Remove Category</a>
        </div>

        <!-- Other Actions Section -->
        <div class="action-section">
            <h3>Other Actions</h3>
            <a href="admin_orders.php" class="action-btn">Orders</a>
            <a href="admin_inventory.php" class="action-btn">Inventory</a>
            <a href="admin_activity_logs.php" class="action-btn">Activity Logs</a>
            <button class="stats-btn" onclick="location.href='statistics.php'">Stats</button>
        </div>
    </div>
</body>
</html>
