<?php
// Connect to the database
require_once 'config/db_connection.php';

// Show Users table structure
$users_result = $conn->query("SHOW COLUMNS FROM Users");
echo "<h2>Users Table Structure:</h2>";
if ($users_result->num_rows > 0) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = $users_result->fetch_assoc()) {
        echo "<tr><td>" . $row["Field"] . "</td><td>" . $row["Type"] . "</td><td>" . $row["Null"] . "</td><td>" . $row["Key"] . "</td><td>" . $row["Default"] . "</td><td>" . $row["Extra"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No columns found in Users table<br>";
}

// Show Orders table structure
$orders_result = $conn->query("SHOW COLUMNS FROM Orders");
echo "<h2>Orders Table Structure:</h2>";
if ($orders_result->num_rows > 0) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = $orders_result->fetch_assoc()) {
        echo "<tr><td>" . $row["Field"] . "</td><td>" . $row["Type"] . "</td><td>" . $row["Null"] . "</td><td>" . $row["Key"] . "</td><td>" . $row["Default"] . "</td><td>" . $row["Extra"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No columns found in Orders table<br>";
}

// Close the database connection
$conn->close();
?> 