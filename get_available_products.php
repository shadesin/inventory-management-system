<?php
// Include database connection
include 'db_connection.php';

// SQL query to get products available in inventory but not in any cart using EXCEPT
$sql = "
    SELECT p.pid, p.title, p.price, p.summary, p.updatedat
    FROM product p
    INNER JOIN inventory i ON p.pid = i.pid
    EXCEPT
    SELECT p.pid, p.title, p.price, p.summary, p.updatedat
    FROM product p
    INNER JOIN inventory i ON p.pid = i.pid
    INNER JOIN cart_items ci ON p.pid = ci.product_id
";

$result = mysqli_query($conn, $sql);

// Check if any products were found
if (mysqli_num_rows($result) > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['pid']}</td>
                <td>{$row['title']}</td>
                <td>{$row['price']}</td>
                <td>{$row['summary']}</td>
                <td>{$row['updatedat']}</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No products available in inventory but not in any cart.</p>";
}

// Close connection
mysqli_close($conn);
?>
