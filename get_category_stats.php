<?php
// Include database connection
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            margin-bottom: 10px;
            color: #333;
        }
        p {
            margin: 0;
            margin-bottom: 10px;
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
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    // Check if category ID is provided
    if (isset($_GET['category'])) {
        $categoryId = $_GET['category'];

        // Ensure the category ID is a valid integer
        if (!is_numeric($categoryId)) {
            echo "<p>No category selected.</p>";
            echo "<a href='admin_dashboard.php' class='back-btn'>Back to Dashboard</a>";
            exit();
        }

        // Fetch category title
        $categoryTitleQuery = "SELECT title FROM category WHERE cid = $categoryId";
        $categoryTitleResult = mysqli_query($conn, $categoryTitleQuery);
        if ($categoryTitleResult) {
            $categoryTitle = mysqli_fetch_assoc($categoryTitleResult)['title'];

            // Fetch child categories, including the current category
            $childCategoriesQuery = "SELECT cid FROM category WHERE parentid = $categoryId OR cid = $categoryId";
            $childCategoriesResult = mysqli_query($conn, $childCategoriesQuery);
            $childCategoryIds = [];
            while ($row = mysqli_fetch_assoc($childCategoriesResult)) {
                $childCategoryIds[] = $row['cid'];
            }
            $childCategoryIdsStr = implode(",", $childCategoryIds);

            // Fetch total number of products in the category and its child categories
            $totalProductsQuery = "SELECT COUNT(DISTINCT pid) AS total_products FROM product_category 
                               WHERE cid IN ($childCategoryIdsStr)";
            $totalProductsResult = mysqli_query($conn, $totalProductsQuery);
            $totalProductsRow = mysqli_fetch_assoc($totalProductsResult);
            $totalProducts = $totalProductsRow['total_products'];

            // Fetch average price of products in the category and its child categories
            $averagePriceQuery = "SELECT AVG(price) AS average_price FROM product 
                              WHERE pid IN (SELECT DISTINCT pid FROM product_category 
                                            WHERE cid IN ($childCategoryIdsStr))";
            $averagePriceResult = mysqli_query($conn, $averagePriceQuery);
            $averagePriceRow = mysqli_fetch_assoc($averagePriceResult);
            $averagePrice = $averagePriceRow['average_price'];

            // Format statistics
            $formattedStats = "<h3>Statistics for Category: $categoryTitle</h3>";
            if ($totalProducts > 0) {
                $formattedStats .= "<p>Total Number of Products: $totalProducts</p>";
                $formattedStats .= "<p>Average Price of Products: ₹" . round($averagePrice, 2) . "</p>";
            } else {
                $formattedStats .= "<p>No products found in this category.</p>";
            }

            // Display statistics
            echo $formattedStats;
        } else {
            echo "<p>Category not found.</p>";
        }

        // Back to Dashboard Button
        echo "<a href='admin_dashboard.php' class='back-btn'>Back to Dashboard</a>";
    }
    ?>

</div>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
