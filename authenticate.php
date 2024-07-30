<?php
session_start();

// Hardcoded credentials (replace with your database logic)
$admin_username = "admin";
$admin_password = "admin";

$supplier_username="supplier";
$supplier_password="supplier";

$salesperson_username="salesperson";
$salesperson_password="salesperson";

$customer_username="customer";
$customer_password="customer";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === $admin_username && $password === $admin_password) {
        // Authentication successful
        // Set session variables and redirect to dashboard
        $_SESSION["username"] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } 
    elseif ($username === $supplier_username && $password === $supplier_password) {
        $_SESSION["username"] = $username;
        header("Location: supplier_dashboard.php");
        exit();
    }
    elseif ($username === $salesperson_username && $password === $salesperson_password) {
        $_SESSION["username"] = $username;
        header("Location: salesperson_dashboard.php");
        exit();
    }
    elseif ($username === $customer_username && $password === $customer_password) {
        $_SESSION["username"] = $username;
        header("Location: customer_dashboard.php");
        exit();
    }
    else {
        // Authentication failed
        // Redirect back to login page with error message
        header("Location: login.php?error=1");
        exit();
    }
}
?>
