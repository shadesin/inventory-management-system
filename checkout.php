<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch cart items from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT ci.product_id, ci.quantity, p.title, p.price FROM cart_items ci
        INNER JOIN product p ON ci.product_id = p.pid
        WHERE ci.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total and prepare for shipping cost calculation
$total = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$subtotal = $total;

// Call calculate_shipping_cost procedure
$stmt = $conn->prepare("CALL calculate_shipping_cost(?, @p_shipping_cost)");
$stmt->bind_param("d", $total);
$stmt->execute();
$stmt->close();

// Fetch the calculated shipping cost
$result = $conn->query("SELECT @p_shipping_cost AS shipping_cost");
$row = $result->fetch_assoc();
$shipping_cost = $row['shipping_cost'];
$total += $shipping_cost;

// Process order on confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if cart is not empty
    if (count($cart_items) > 0) {
        // Call process_order procedure
        $stmt = $conn->prepare("CALL process_order(?, ?, ?, ?)");
        $stmt->bind_param("iddd", $user_id, $total, $subtotal, $shipping_cost);
        $stmt->execute();
        $stmt->close();

        // Clear cart after order confirmation
        $sql = "DELETE FROM cart_items WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Redirect to order confirmation page
        header("Location: order_confirmation.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        .confirm-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .confirm-btn:hover {
            background-color: #45a049;
        }
        .back-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h2>Checkout</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display cart items
            foreach ($cart_items as $item) {
                $item_total = $item['price'] * $item['quantity'];
                echo "<tr>";
                echo "<td>{$item['title']}</td>";
                echo "<td>₹ {$item['price']}</td>";
                echo "<td>{$item['quantity']}</td>";
                echo "<td>₹ {$item_total}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <h3>Shipping: ₹ <?php echo number_format($shipping_cost, 2); ?></h3>
    <h3>Subtotal: ₹ <?php echo number_format($subtotal, 2); ?></h3>
    <h3>Total: ₹ <?php echo number_format($total, 2); ?></h3>
    <form method="post">
        <button type="submit" class="confirm-btn">Confirm Order</button>
    </form>
    <a href="view_cart.php" class="back-btn">Back to Cart</a>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
