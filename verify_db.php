<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_quiz_db";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if tbl_users exists and has the correct structure
    $result = $conn->query("SHOW COLUMNS FROM tbl_users");
    if ($result) {
        echo "tbl_users table exists. Columns:<br>";
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "tbl_users table does not exist or has incorrect structure.<br>";
        echo "Error: " . $conn->error . "<br>";
    }

    // Check if we can query the email column
    $test_query = $conn->query("SELECT email FROM tbl_users LIMIT 1");
    if ($test_query) {
        echo "<br>Email column is accessible.<br>";
    } else {
        echo "<br>Error accessing email column: " . $conn->error . "<br>";
    }

    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 