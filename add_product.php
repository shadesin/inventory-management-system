
<?php
// add_product.php

session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch child categories from the database
$sql = "SELECT cid, title FROM category WHERE parentid IS NOT NULL";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional styles for the form */
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <form action="process_add_product.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="summary">Summary:</label>
            <input type="text" id="summary" name="summary">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <label for="category">Category:</label>
            <select id="category" name="category">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['cid']; ?>"><?php echo $category['title']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
