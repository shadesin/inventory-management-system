<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Check if category ID is provided
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Include database connection
    include 'db_connection.php';
    include 'log_activity.php';

    // Function to delete products associated with a category
function deleteProducts($category_id, $conn) {
    // Fetch product IDs associated with the category
    $sql = "SELECT pid FROM product_category WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Delete products from product_category table
    $sql_delete_product_category = "DELETE FROM product_category WHERE cid = ?";
    $stmt_delete_product_category = $conn->prepare($sql_delete_product_category);
    $stmt_delete_product_category->bind_param("i", $category_id);
    $stmt_delete_product_category->execute();

    // Delete products from product table
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['pid'];
        // Delete product from product table
        $sql_delete_product = "DELETE FROM product WHERE pid = ?";
        $stmt_delete_product = $conn->prepare($sql_delete_product);
        $stmt_delete_product->bind_param("i", $product_id);
        $stmt_delete_product->execute();

        // Delete product from cart_items table
        $sql_delete_cart_item = "DELETE FROM cart_items WHERE product_id = ?";
        $stmt_delete_cart_item = $conn->prepare($sql_delete_cart_item);
        $stmt_delete_cart_item->bind_param("i", $product_id);
        $stmt_delete_cart_item->execute();
    }
}

    // Function to delete child categories recursively
    function deleteChildCategories($parent_id, $conn) {
        // Fetch child categories
        $sql = "SELECT cid FROM category WHERE parentid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Delete child categories recursively
        while ($row = $result->fetch_assoc()) {
            $child_id = $row['cid'];
            // Delete child categories
            deleteChildCategories($child_id, $conn);
            // Delete products associated with child categories
            deleteProducts($child_id, $conn);
            // Delete child category
            $sql_delete_child_category = "DELETE FROM category WHERE cid = ?";
            $stmt_delete_child_category = $conn->prepare($sql_delete_child_category);
            $stmt_delete_child_category->bind_param("i", $child_id);
            $stmt_delete_child_category->execute();
        }
    }

    // Delete products associated with the category
    deleteProducts($category_id, $conn);

    // Delete child categories recursively and the category itself
    deleteChildCategories($category_id, $conn);

    // Delete the category itself
    $sql_delete_category = "DELETE FROM category WHERE cid = ?";
    $stmt_delete_category = $conn->prepare($sql_delete_category);
    $stmt_delete_category->bind_param("i", $category_id);
    $stmt_delete_category->execute();

    // Log activity
    $user_id = $_SESSION["user_id"];
    $activity = "Removed a category";
    log_activity($user_id, $activity);

    // Close statement and connection
    $stmt_delete_category->close();
    mysqli_close($conn);

    // Redirect back to delete_category.php after deletion
    header("Location: delete_category.php");
    exit();
} else {
    // Redirect to delete_category.php if category ID is not provided
    header("Location: delete_category.php");
    exit();
}
?>
