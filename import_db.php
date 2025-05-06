<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_quiz_db";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select the database
    $conn->select_db($dbname);

    // Drop existing tables if they exist
    $conn->query("DROP TABLE IF EXISTS tbl_users");
    $conn->query("DROP TABLE IF EXISTS tbl_quiz");
    $conn->query("DROP TABLE IF EXISTS tbl_result");
    $conn->query("DROP TABLE IF EXISTS tbl_passcodes");
    $conn->query("DROP TABLE IF EXISTS tbl_passcode_counter");

    // Create tables
    $sql = "
    CREATE TABLE `tbl_quiz` (
        `tbl_quiz_id` int(11) NOT NULL AUTO_INCREMENT,
        `quiz_question` text NOT NULL,
        `option_a` text NOT NULL,
        `option_b` text NOT NULL,
        `option_c` text NOT NULL,
        `option_d` text NOT NULL,
        `correct_answer` text NOT NULL,
        PRIMARY KEY (`tbl_quiz_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE `tbl_result` (
        `tbl_result_id` int(11) NOT NULL AUTO_INCREMENT,
        `quiz_taker` text NOT NULL,
        `year_section` text NOT NULL,
        `total_score` int(11) NOT NULL,
        `date_taken` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`tbl_result_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE `tbl_users` (
        `user_id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` enum('teacher','student') NOT NULL,
        `passcode` varchar(4) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`user_id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE `tbl_passcodes` (
        `passcode_id` int(11) NOT NULL AUTO_INCREMENT,
        `passcode` varchar(4) NOT NULL,
        `year` int(4) NOT NULL,
        `is_used` tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (`passcode_id`),
        UNIQUE KEY `passcode` (`passcode`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

    CREATE TABLE `tbl_passcode_counter` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `next_passcode` int(4) NOT NULL DEFAULT 2020,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";

    // Execute multi query
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    }

    // Wait for all queries to complete
    while ($conn->more_results() && $conn->next_result());

    // Insert initial passcodes
    $passcodes = array();
    for ($year = 2020; $year <= 2040; $year++) {
        $passcodes[] = "('$year', $year, 0)";
    }
    $passcode_values = implode(',', $passcodes);
    $conn->query("INSERT INTO tbl_passcodes (passcode, year, is_used) VALUES $passcode_values");

    // Insert initial counter value
    $conn->query("INSERT INTO tbl_passcode_counter (next_passcode) VALUES (2020)");

    // Insert sample quiz data
    $conn->query("INSERT INTO tbl_quiz (quiz_question, option_a, option_b, option_c, option_d, correct_answer) VALUES
        ('What is HTML stands for?', 'How To Make Lumpia', 'Hyper Tronic Mongo Logic', 'Hard To Make Love', 'HyperText Markup Language', 'D'),
        ('What is the original acronym of PHP?', 'Hypertext Preprocessor', 'Personal Home Page', 'Programming Happy Pill', 'None of the above', 'B'),
        ('CSS is fundamental to?', 'Databases', 'Web design', 'Server-side', 'None of the above', 'B')");

    echo "Database imported successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 