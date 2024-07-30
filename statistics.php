<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch categories for dropdown
$categories_sql = "SELECT cid, title FROM category";
$categories_result = mysqli_query($conn, $categories_sql);

// Function to get category statistics
function getCategoryStats($conn, $categoryId) {
    $sql = "SELECT p.title, SUM(ci.quantity) as total_quantity, SUM(ci.quantity * p.price) as total_sales
            FROM cart_items ci
            INNER JOIN product p ON ci.product_id = p.pid
            WHERE p.category_id = ?
            GROUP BY p.title";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    return $stmt->get_result();
}

// Initialize variables for nested query
$threshold = 0;
$users_result = null;

// Handle form submission for nested query
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["threshold"])) {
    $threshold = intval($_POST["threshold"]);
    $users_sql = "
        SELECT u.username
        FROM users u
        WHERE u.id IN (
            SELECT o.cid
            FROM orderdetails o
            GROUP BY o.cid
            HAVING COUNT(o.orderid) > ?
        )
    ";
    $stmt = $conn->prepare($users_sql);
    $stmt->bind_param("i", $threshold);
    $stmt->execute();
    $users_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional Styles */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        select, input[type="number"], button {
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Statistics</h2>

        <!-- Category Dropdown -->
        <label for="category">Select Category:</label>
        <select id="category" name="category" onchange="getCategoryStats(this.value)">
            <option value="">Select Category</option>
            <?php while ($row = mysqli_fetch_assoc($categories_result)): ?>
                <option value="<?php echo $row['cid']; ?>"><?php echo $row['title']; ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Statistics Table -->
        <div id="categoryStats"></div>

        <!-- Form to input order threshold -->
        <h3>Find Users with More Than a Certain Number of Orders</h3>
        <form method="post" action="">
            <label for="threshold">Enter Number of Orders:</label>
            <input type="number" id="threshold" name="threshold" value="<?php echo $threshold; ?>" required>
            <button type="submit">Submit</button>
        </form>

        <!-- Users Table -->
        <?php if ($users_result && $users_result->num_rows > 0): ?>
            <h3>Users with More Than <?php echo $threshold; ?> Orders</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($users_result): ?>
            <p>No users found with more than <?php echo $threshold; ?> orders.</p>
        <?php endif; ?>

        <!-- Products Available but Not in Cart -->
        <h3>Products Available in Inventory but Not in Any Cart</h3>
        <button onclick="getAvailableProducts()">Show Products</button>
        <div id="availableProducts"></div>
    </div>

    <script>
        // Function to fetch category statistics
        function getCategoryStats(categoryId) {
            if (categoryId === "") {
                document.getElementById("categoryStats").innerHTML = "";
                return;
            }

            // Create an AJAX request
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("categoryStats").innerHTML = this.responseText;
                }
            };
            xhr.open("GET", "get_category_stats.php?category=" + categoryId, true);
            xhr.send();
        }

        // Function to fetch products available in inventory but not in any cart
        function getAvailableProducts() {
            // Create an AJAX request
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("availableProducts").innerHTML = this.responseText;
                }
            };
            xhr.open("GET", "get_available_products.php", true);
            xhr.send();
        }
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
