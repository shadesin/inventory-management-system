<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Initialize variables for search functionality
$search = '';
$category = 'all';

// Check if search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST["search"];
    $category = $_POST["category"];
}

// Fetch products with category details and quantity from the inventory table
$sql = "SELECT p.pid, p.title, p.summary, p.price, p.createdat, p.updatedat, c.title AS category, c.parentid AS parent_category_id,
               cp.title AS parent_category, i.quantity AS quantity
        FROM product p
        LEFT JOIN product_category pc ON p.pid = pc.pid
        LEFT JOIN category c ON pc.cid = c.cid
        LEFT JOIN category cp ON c.parentid = cp.cid
        LEFT JOIN inventory i ON p.pid = i.pid";

// Add search and category filters if provided
if (!empty($search)) {
    $sql .= " WHERE p.title LIKE '%$search%' OR c.title LIKE '%$search%' OR cp.title LIKE '%$search%'";
}

if ($category !== 'all') {
    $sql .= empty($search) ? " WHERE " : " AND ";
    $sql .= " (c.title = '$category' OR cp.title = '$category')";
}

// Order by parent category and category
$sql .= " ORDER BY parent_category, category";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Your existing CSS styles */
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #ff0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #cc0000;
        }
        /* Additional styles for table and buttons */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .edit-btn {
        background-color: #3498db; /* Blue color */
        color: #fff;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .edit-btn:hover {
        background-color: #2980b9; /* Darker blue color on hover */
    }
    .delete-btn {
            background-color: #e74c3c; /* Red color */
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .delete-btn:hover {
            background-color: #8b0000; /* Dark red color */
        }
        .actions {
            white-space: nowrap;
        }
        .back-btn {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Inventory</h2>
        
        <!-- Search Form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" value="<?php echo $search; ?>">
            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="all" <?php if ($category === 'all') echo 'selected'; ?>>All Categories</option>
                <?php
                // Fetch categories for dropdown
                $categories_sql = "SELECT title FROM category";
                $categories_result = mysqli_query($conn, $categories_sql);
                while ($cat_row = mysqli_fetch_assoc($categories_result)) {
                    echo "<option value='" . $cat_row['title'] . "'";
                    if ($category === $cat_row['title']) echo ' selected';
                    echo ">" . $cat_row['title'] . "</option>";
                }
                ?>
            </select>
            <button type="submit">Search</button>
        </form>

        <!-- Display Products -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Title</th> <!-- Increased width for title column -->
                    <th style="width: 30%;">Summary</th> <!-- Increased width for summary column -->
                    <th>Price</th>
                    <th>Category</th>
                    <th>Parent Category</th>
                    <th>Quantity</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['summary']; ?></td>
                        <td>₹<?php echo $row['price']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo $row['parent_category']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['createdat']; ?></td>
                        <td><?php echo $row['updatedat']; ?></td>
                        <td class="actions">
                            <a href='edit_product.php?pid=<?php echo $row['pid']; ?>' class='edit-btn'>Edit</a> |
                            <a href='delete_product.php?pid=<?php echo $row['pid']; ?>' class='delete-btn'>Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back to Dashboard Button -->
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
