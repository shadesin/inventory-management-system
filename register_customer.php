<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'db_connection.php';

    // Retrieve form data
    $new_username = $_POST["new_username"];
    $new_password = $_POST["new_password"];

    // Check if the username already exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $new_username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Username already exists
        $error_message = "Username already exists. Please choose a different one.";
    } else {
        // Define the role for new customers
        $role = "customer";

        // Insert new user into the database
        $insert_sql = "INSERT INTO users (role, username, password) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $role, $new_username, $new_password);

        if ($insert_stmt->execute()) {
            // User registered successfully
            header("Location: login.php");
            exit();
        } else {
            // Error registering user
            $error_message = "Error registering user: " . $conn->error;
        }

        // Close statement
        $insert_stmt->close();
    }

    // Close connection
    mysqli_close($conn);
} else {
    // Redirect to login page if accessed directly without form submission
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Customer</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .error-message {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <h2>Register Customer</h2>
    <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
    <form method="post">
        <label for="new_username">New Username:</label>
        <input type="text" id="new_username" name="new_username" required><br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
