<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_POST["product_id"]) || empty($_POST["product_id"])) {
    header("Location: view_products.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Retrieve product ID from the form
$product_id = $_POST["product_id"];
$user_id = $_SESSION["user_id"]; // Get user ID from session

// Check if the item is available in the inventory
$sql_quantity = "SELECT quantity FROM inventory WHERE pid = ?";
$stmt_quantity = $conn->prepare($sql_quantity);
$stmt_quantity->bind_param("i", $product_id);
$stmt_quantity->execute();
$result_quantity = $stmt_quantity->get_result();

if ($result_quantity->num_rows > 0) {
    $row = $result_quantity->fetch_assoc();
    $quantity = $row['quantity'];

    if ($quantity <= 0) {
        // Item is out of stock
        echo "<div style='color: red; font-weight: bold; text-align: center;'>Sorry, this item is currently out of stock!</div>";
        exit();
    }
} else {
    // Item not found in inventory
    echo "<div style='color: red; font-weight: bold; text-align: center;'>Sorry, this item is not available!</div>";
    exit();
}

// Check if the item already exists in the cart
$sql_cart = "SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("ii", $user_id, $product_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    // Item already exists in the cart, update quantity
    $row_cart = $result_cart->fetch_assoc();
    $new_quantity = $row_cart['quantity'] + 1;
    $sql_update_cart = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt_update_cart = $conn->prepare($sql_update_cart);
    $stmt_update_cart->bind_param("iii", $new_quantity, $user_id, $product_id);
    if ($stmt_update_cart->execute()) {
        // Item quantity updated successfully
        echo "<div style='color: green; font-weight: bold; text-align: center;'>Item quantity updated in your cart!</div>";
    } else {
        // Error occurred while updating item quantity
        echo "<div style='color: red; font-weight: bold; text-align: center;'>Error: Unable to update item quantity. Please try again.</div>";
    }
} else {
    // Insert new item into the cart
    $sql_insert = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $product_id);
    if ($stmt_insert->execute()) {
        // Item added to cart successfully
        echo "<div style='color: green; font-weight: bold; text-align: center;'>Item added to your cart!</div>";
    } else {
        // Error occurred while adding item to cart
        echo "<div style='color: red; font-weight: bold; text-align: center;'>Error: Unable to add item to cart. Please try again.</div>";
    }
}

// Decrease the quantity of the item in the inventory and update the `updatedat` field in the `product` table
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");
$sql_update_quantity = "UPDATE inventory SET quantity = quantity - 1 WHERE pid = ?";
$sql_update_product = "UPDATE product SET updatedat = ? WHERE pid = ?";
$stmt_update_quantity = $conn->prepare($sql_update_quantity);
$stmt_update_product = $conn->prepare($sql_update_product);
$stmt_update_quantity->bind_param("i", $product_id);
$stmt_update_product->bind_param("si", $currentDateTime, $product_id);
$stmt_update_quantity->execute();
$stmt_update_product->execute();

// Close connection
$stmt_update_quantity->close();
$stmt_update_product->close();
$conn->close();
?>
