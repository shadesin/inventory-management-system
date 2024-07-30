<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Function to remove one instance of an item from the cart
function removeFromCart($conn, $user_id, $product_id) {
    // Decrease quantity in cart
    $sql_decrease = "UPDATE cart_items SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?";
    $stmt_decrease = $conn->prepare($sql_decrease);
    $stmt_decrease->bind_param("ii", $user_id, $product_id);
    $stmt_decrease->execute();
    
    // If quantity becomes 0, delete the item from cart
    $sql_delete = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND quantity <= 0";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $product_id);
    $stmt_delete->execute();

    // Increase quantity in inventory
    $sql_increase_inventory = "UPDATE inventory SET quantity = quantity + 1 WHERE pid = ?";
    $stmt_increase_inventory = $conn->prepare($sql_increase_inventory);
    $stmt_increase_inventory->bind_param("i", $product_id);
    $stmt_increase_inventory->execute();
}

// Check if cart item should be removed
if (isset($_GET['remove']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    removeFromCart($conn, $_SESSION["user_id"], $product_id);
    header("Location: view_cart.php");
    exit();
}

// Fetch cart items from the view
$user_id = $_SESSION["user_id"];
$sql = "SELECT product_id, quantity, title, price FROM view_cart_items WHERE user_id = ?";
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
    <title>View Cart</title>
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
        .proceed-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .proceed-btn:hover {
            background-color: #45a049;
        }
        .remove-btn {
            padding: 5px 10px;
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .remove-btn:hover {
            background-color: #e53935;
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
    <h2>Cart</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display cart items
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_price = $row['quantity'] * $row['price'];
                    echo "<tr>";
                    echo "<td>{$row['title']}</td>";
                    echo "<td>{$row['quantity']}</td>";
                    if ($row['quantity'] > 1) {
                        echo "<td>{$row['quantity']} x ₹{$row['price']} = ₹{$total_price}</td>";
                    } else {
                        echo "<td>₹{$row['price']}</td>";
                    }
                    echo "<td><a href='view_cart.php?remove=true&product_id={$row['product_id']}' class='remove-btn'>Remove</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Your cart is empty.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="checkout.php" class="proceed-btn">Proceed to Checkout</a>
    <a href="view_products.php" class="back-btn">Back to Products</a>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
