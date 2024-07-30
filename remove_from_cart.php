<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided in the URL
if (!isset($_GET["product_id"]) || empty($_GET["product_id"])) {
    header("Location: view_cart.php");
    exit();
}

// Retrieve product ID from URL
$product_id = $_GET["product_id"];

// Check if product exists in the cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !in_array($product_id, $_SESSION['cart'])) {
    header("Location: view_cart.php");
    exit();
}

// Remove product from the cart
$key = array_search($product_id, $_SESSION['cart']);
unset($_SESSION['cart'][$key]);

// Redirect back to the cart page
header("Location: view_cart.php");
exit();
?>
