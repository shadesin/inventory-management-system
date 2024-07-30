<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch orders for the current user from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM orderdetails WHERE cid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
        a.back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        a.back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h2>Order History</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Status</th>
                <th>Subtotal</th>
                <th>Shipping</th>
                <th>Total</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display order details
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['orderid']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>₹ {$row['subtotal']}</td>";
                echo "<td>₹ {$row['shipping']}</td>";
                echo "<td>₹ {$row['total']}</td>";
                echo "<td>{$row['createdat']}</td>";
                echo "<td>{$row['updatedat']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="customer_dashboard.php" class="back-btn">Back to Dashboard</a>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
