<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "customer") {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch products with category information from the database
$sql = "SELECT p.pid, p.title, p.price, p.summary, c.title AS category, cp.title AS parent_category, i.quantity
        FROM product p
        LEFT JOIN product_category pc ON p.pid = pc.pid
        LEFT JOIN category c ON pc.cid = c.cid
        LEFT JOIN category cp ON c.parentid = cp.cid
        LEFT JOIN inventory i ON p.pid = i.pid";

// Check if search query is provided
if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " WHERE p.title LIKE '%$search%' OR c.title LIKE '%$search%' OR cp.title LIKE '%$search%'";
}

// Check if category filter is provided
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
    $category = $_GET['category'];
    // Check if WHERE clause already exists
    $sql .= empty($search) ? " WHERE " : " AND ";
    $sql .= " (c.title = '$category' OR cp.title = '$category')";
}

// Check if sorting by price range is requested
if (isset($_GET['sort']) && $_GET['sort'] === 'price') {
    $sql .= " ORDER BY p.price";
}

$result = mysqli_query($conn, $sql);

// Check if products exist
if (mysqli_num_rows($result) > 0) {
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Products</title>
        <link rel="stylesheet" href="styles.css">
        <style>
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
            .back-btn {
                background-color: #3498db;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
            }
            .back-btn:hover {
                background-color: #2980b9;
            }
            .add-to-cart-btn {
        background-color: #f39c12; /* Yellow color */
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .add-to-cart-btn:hover {
        background-color: #d68910; /* Darker yellow color on hover */
    }

    .cart-btn {
        background-color: #27ae60; /* Green color */
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .cart-btn:hover {
        background-color: #219d53; /* Darker green color on hover */
    }
            .search-form {
                margin-bottom: 20px;
            }
            .search-input, .category-select {
                padding: 8px;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
        <h2>View Products</h2>
            <div class="search-form">
                <form method="get">
                    <input type="text" name="search" class="search-input" placeholder="Search by name">
                    <select name="category" class="category-select">
                        <option value="all">All Categories</option>
                        <?php
                        // Fetch distinct categories
                        $categoryQuery = "SELECT DISTINCT title FROM category";
                        $categoryResult = mysqli_query($conn, $categoryQuery);
                        while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                            echo "<option value='{$categoryRow['title']}'>{$categoryRow['title']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Quantity Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['title']; ?></td>
                            <td>₹<?php echo $row['price']; ?></td>
                            <td><?php echo $row['summary']; ?></td>
                            <td><?php echo $row['category'] . ' (' . $row['parent_category'] . ')'; ?></td>
                            <td>
                                <?php
                                // Display quantity or "Out of stock!" if quantity is 0
                                if ($row['quantity'] > 0) {
                                    echo $row['quantity'];
                                } else {
                                    echo "Out of stock!";
                                }
                                ?>
                            </td>
                            <td>
                                <form method="post" action="add_to_cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['pid']; ?>">
                                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button onclick="location.href='view_cart.php'" class="cart-btn">View Cart</button>
            <button onclick="location.href='customer_dashboard.php'" class="back-btn">Back to Dashboard</button>
        </div>
    </body>
    </html>

    <?php
} else {
    // No products found
    echo "No products available.";
}

// Close connection
mysqli_close($conn);
?>
