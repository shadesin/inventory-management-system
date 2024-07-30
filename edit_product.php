<?php
// edit_product.php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if product ID is provided
if (!isset($_GET['pid'])) {
    echo "Product ID is missing.";
    exit();
}

// Get product ID from query parameters
$pid = $_GET['pid'];

// Fetch product details from database
$sql = "SELECT p.*, c.cid, c.title AS category_title, i.quantity AS quantity 
        FROM product p 
        LEFT JOIN product_category pc ON p.pid = pc.pid 
        LEFT JOIN category c ON pc.cid = c.cid 
        LEFT JOIN inventory i ON p.pid = i.pid
        WHERE p.pid = '$pid'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    exit();
}

if (mysqli_num_rows($result) == 0) {
    echo "Product not found.";
    exit();
}

// Fetch product details
$product = mysqli_fetch_assoc($result);

// Fetch categories from database
$category_sql = "SELECT cid, title FROM category";
$category_result = mysqli_query($conn, $category_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated product data from form
    $title = $_POST['title'];
    $summary = $_POST['summary'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    // Ensure quantity is not negative
    if ($quantity < 0) {
        $quantity = 0;
    }

    // Update product in database
    $update_sql = "UPDATE product SET title='$title', summary='$summary', price='$price', updatedat=NOW() WHERE pid='$pid'";
    if (mysqli_query($conn, $update_sql)) {
        // Update product category in product_category table
        $update_category_sql = "UPDATE product_category SET cid='$category' WHERE pid='$pid'";
        mysqli_query($conn, $update_category_sql);

        // Update quantity in inventory table
        $update_quantity_sql = "UPDATE inventory SET quantity='$quantity' WHERE pid='$pid'";
        mysqli_query($conn, $update_quantity_sql);

        // Include log_activity function
        include 'log_activity.php';
        // Example of logging activity
        $user_id = $_SESSION["user_id"]; // Assuming you store user ID in session
        $activity = "Edited a product";
        log_activity($user_id, $activity);

        // Product updated successfully, redirect to inventory page
        header("Location: admin_inventory.php");
        exit();
    } else {
        // Error updating product
        echo "Error: " . $update_sql . "<br>" . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>
        <form action="" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $product['title']; ?>" required>
            <label for="summary">Summary:</label>
            <input type="text" id="summary" name="summary" value="<?php echo $product['summary']; ?>">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required>
            <label for="category">Category:</label>
            <select id="category" name="category">
                <?php while ($category = mysqli_fetch_assoc($category_result)): ?>
                    <option value="<?php echo $category['cid']; ?>" <?php if ($category['cid'] == $product['cid']) echo 'selected'; ?>>
                        <?php echo $category['title']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Update Product</button>
        </form>
    </div>
    <script>
        // Client-side validation to ensure quantity is not negative
        document.getElementById("quantity").addEventListener("change", function() {
            var quantityInput = document.getElementById("quantity");
            if (quantityInput.value < 0) {
                quantityInput.value = 0;
            }
        });
    </script>
</body>
</html>
