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
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    // Select the database
    $conn->select_db("online_quiz_db");

    // Drop existing tables if they exist
    $conn->query("DROP TABLE IF EXISTS tbl_users");
    $conn->query("DROP TABLE IF EXISTS tbl_quiz");
    $conn->query("DROP TABLE IF EXISTS tbl_result");
    $conn->query("DROP TABLE IF EXISTS tbl_passcodes");
    $conn->query("DROP TABLE IF EXISTS tbl_passcode_counter");

    // Create tbl_users table with correct structure
    $sql = "CREATE TABLE tbl_users (
        user_id INT(11) NOT NULL AUTO_INCREMENT,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('teacher', 'student') NOT NULL,
        passcode VARCHAR(4) DEFAULT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        UNIQUE KEY email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        echo "Table tbl_users created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // Create other tables
    $sql = "CREATE TABLE tbl_quiz (
        tbl_quiz_id INT(11) NOT NULL AUTO_INCREMENT,
        quiz_question TEXT NOT NULL,
        option_a TEXT NOT NULL,
        option_b TEXT NOT NULL,
        option_c TEXT NOT NULL,
        option_d TEXT NOT NULL,
        correct_answer TEXT NOT NULL,
        PRIMARY KEY (tbl_quiz_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        echo "Table tbl_quiz created successfully<br>";
    }

    $sql = "CREATE TABLE tbl_result (
        tbl_result_id INT(11) NOT NULL AUTO_INCREMENT,
        quiz_taker TEXT NOT NULL,
        year_section TEXT NOT NULL,
        total_score INT(11) NOT NULL,
        date_taken TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (tbl_result_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        echo "Table tbl_result created successfully<br>";
    }

    $sql = "CREATE TABLE tbl_passcodes (
        passcode_id INT(11) NOT NULL AUTO_INCREMENT,
        passcode VARCHAR(4) NOT NULL,
        year INT(4) NOT NULL,
        is_used TINYINT(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (passcode_id),
        UNIQUE KEY passcode (passcode)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        echo "Table tbl_passcodes created successfully<br>";
    }

    $sql = "CREATE TABLE tbl_passcode_counter (
        id INT(11) NOT NULL AUTO_INCREMENT,
        next_passcode INT(4) NOT NULL DEFAULT 2020,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        echo "Table tbl_passcode_counter created successfully<br>";
    }

    // Insert initial data
    $sql = "INSERT INTO tbl_passcode_counter (next_passcode) VALUES (2020)";
    if ($conn->query($sql) === TRUE) {
        echo "Initial data inserted successfully<br>";
    }

    // Insert sample quiz data
    $sql = "INSERT INTO tbl_quiz (quiz_question, option_a, option_b, option_c, option_d, correct_answer) VALUES
        ('What is HTML stands for?', 'How To Make Lumpia', 'Hyper Tronic Mongo Logic', 'Hard To Make Love', 'HyperText Markup Language', 'D'),
        ('What is the original acronym of PHP?', 'Hypertext Preprocessor', 'Personal Home Page', 'Programming Happy Pill', 'None of the above', 'B'),
        ('CSS is fundamental to?', 'Databases', 'Web design', 'Server-side', 'None of the above', 'B')";

    if ($conn->query($sql) === TRUE) {
        echo "Sample quiz data inserted successfully<br>";
    }

    $conn->close();
    echo "<br>Database setup completed successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 