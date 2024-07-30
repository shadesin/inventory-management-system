<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';
include 'log_activity.php';

// Fetch categories from the database with parent category titles
$sql = "SELECT c1.cid, c1.parentid, c1.title, c2.title AS parent_title 
        FROM category c1 
        LEFT JOIN category c2 ON c1.parentid = c2.cid";
$result = mysqli_query($conn, $sql);

// Check if any categories exist
if (mysqli_num_rows($result) > 0) {
    // Categories exist, fetch and display them
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    // No categories found
    $categories = [];
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #ff3333;
        }
        .back-btn {
            background-color: #3498db;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h2>Delete Category</h2>
    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    <?php if (!empty($categories)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Parent Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['cid']; ?></td>
                        <td><?php echo $category['title']; ?></td>
                        <td><?php echo isset($category['parent_title']) ? $category['parent_title'] : 'No Parent'; ?></td>
                        <td><button class="delete-btn" onclick="confirmDelete(<?php echo $category['cid']; ?>)">Delete</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No categories found.</p>
    <?php endif; ?>

    <script>
        function confirmDelete(categoryId) {
            if (confirm("Are you sure you want to delete this category?")) {
                window.location.href = "delete_category_process.php?id=" + categoryId;
            }
        }
    </script>
</body>
</html>
