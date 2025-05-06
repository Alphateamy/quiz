<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Create connection without database
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS online_quiz_db";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists<br>";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->close();
    
    // Now include the import script
    include 'import_db.php';
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 