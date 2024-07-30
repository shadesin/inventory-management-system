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
    <title>Admin Orders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            position: relative;
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

        .logout-btn:hover {
            background-color: #cc0000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-btn {
            padding: 5px 3px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 5px; /* Added vertical margin between action buttons */
        }

        .add-btn {
            background-color: #3498db;
            color: #fff;
        }

        .edit-btn {
            background-color: #ffeb3b; /* Changed edit button color to yellow */
            color: #000; /* Changed text color to black */
        }

        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }

        .view-btn {
            background-color: #9b59b6;
            color: #fff;
        }

        .back-btn {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }

        /* Added style for the separator */
        .separator {
            margin: 0 5px; /* Adjust the spacing between buttons */
        }

        /* Adjusted width of action button column */
        .action-column {
            width: 200px; /* Adjust the width as needed */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin!</h2>
        <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>

        <h3>Manage Orders</h3>

        <div>
            <a href="add_order.php" class="action-btn add-btn">Add Order</a>
        </div>

        <?php
        // Include database connection
        include 'db_connection.php';

        // Fetch orders from the database
        $sql = "SELECT orderid, cid, status, subtotal, shipping, createdat, updatedat FROM orderdetails";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>Order ID</th><th>Username</th><th>Order Status</th><th>Subtotal</th><th>Shipping</th><th>Total Cost</th><th>Created At</th><th>Updated At</th><th class='action-column'>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                // Calculate total cost
                $totalCost = $row['subtotal'] + $row['shipping'];

                // Fetch customer name from the users table based on UID
                $uid = $row['cid'];
                $customerQuery = "SELECT username FROM users WHERE id = $uid";
                $customerResult = mysqli_query($conn, $customerQuery);
                $customerData = mysqli_fetch_assoc($customerResult);
                $customerName = $customerData['username'];

                echo "<tr>";
                echo "<td>{$row['orderid']}</td>";
                echo "<td>$customerName</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['subtotal']}</td>";
                echo "<td>{$row['shipping']}</td>";
                echo "<td>$totalCost</td>";
                echo "<td>{$row['createdat']}</td>";
                echo "<td>{$row['updatedat']}</td>";
                echo "<td class='action-column'>
                        <a href='edit_order.php?orderid={$row['orderid']}' class='action-btn edit-btn'>Edit</a>
                        <span class='separator'>|</span>
                        <a href='delete_order.php?orderid={$row['orderid']}' class='action-btn delete-btn'>Delete</a>
                    </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No orders found";
        }

        // Close connection
        mysqli_close($conn);
        ?>

        <!-- Back to Dashboard Button -->
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
