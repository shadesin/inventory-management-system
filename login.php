<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php';
    include 'log_activity.php';

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, role FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $row["role"];

        // Log login activity
        $activity = "Logged in";
        log_activity($_SESSION["user_id"], $activity);

        // Redirect to different dashboards based on role
        if ($_SESSION["role"] == "admin") {
            header("Location: admin_dashboard.php");
        } elseif ($_SESSION["role"] == "customer") {
            header("Location: customer_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
    <style>
        h1 {
            text-align: center;
        }
        .error-message {
            text-align: center;
            color: red;
        }
        .register-btn {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 20px auto;
        }
        .register-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>Inventory Control System</h1>
    <h2>Login</h2>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

    <!-- Additional form for new customers -->
    <h2>New Customer? Register Here</h2>
    <form method="post" action="register_customer.php">
        <label for="new_username">New Username:</label>
        <input type="text" id="new_username" name="new_username" required><br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>
        <button type="submit" class="register-btn">Register</button>
    </form>
</body>
</html>
