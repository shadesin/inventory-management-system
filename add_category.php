<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';



// Fetch parent categories from the database
$sql = "SELECT cid, title FROM category WHERE parentid IS NULL";
$result = mysqli_query($conn, $sql);
$parent_categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $parent_id = $_POST['parent_id'] != 'null' ? $_POST['parent_id'] : null;

    // Insert new category into database
    $sql = "INSERT INTO category (parentid, title) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $parent_id, $title);

    if ($stmt->execute()) {
        // Category added successfully
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Error adding category
        $error_message = "Error adding category: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Category</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your custom styles here */
        .container {
            text-align: center;
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
            margin-top: 20px;
            display: inline-block;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Category</h2>
        <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="title">Category Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="parent_id">Parent Category:</label>
            <select id="parent_id" name="parent_id">
                <option value="null">No Parent Category</option>
                <?php foreach ($parent_categories as $category): ?>
                    <option value="<?php echo $category['cid']; ?>"><?php echo $category['title']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add Category</button>
        </form>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
