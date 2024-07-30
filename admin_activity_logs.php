<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Logs</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            position: relative;
        }

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
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            margin-right: 10px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }

        .clear-btn {
            background-color: #ff0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .clear-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Activity Logs</h2>
        <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
        
        <!-- Back to Dashboard and Clear Logs Buttons -->
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        <button class="clear-btn" onclick="clearLogs()">Clear Logs</button>

        <!-- Display user activity logs -->
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Activity</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'db_connection.php';

                // Fetch user activity logs from database
                $sql = "SELECT a.user_id, a.activity, a.timestamp, u.username, u.role FROM user_activity a INNER JOIN users u ON a.user_id = u.id";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['user_id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['role']}</td>";
                        echo "<td>{$row['activity']}</td>";
                        echo "<td>{$row['timestamp']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No user activity logs found</td></tr>";
                }

                // Close connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function clearLogs() {
            if (confirm("Are you sure you want to clear all logs?")) {
                // Send an AJAX request to clear logs
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "clear_logs.php", true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Refresh the page after logs are cleared
                        window.location.reload();
                    }
                };
                xhr.send();
            }
        }
    </script>
</body>
</html>
