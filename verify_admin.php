<?php
require_once 'config.php';

// Check if tbl_admin exists
$result = $conn->query("SHOW TABLES LIKE 'tbl_admin'");
if ($result->num_rows === 0) {
    echo "tbl_admin table does not exist. Creating it...<br>";
    
    // Create tbl_admin table
    $sql = "CREATE TABLE tbl_admin (
        admin_id INT(11) NOT NULL AUTO_INCREMENT,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (admin_id),
        UNIQUE KEY email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    if ($conn->query($sql)) {
        echo "tbl_admin table created successfully<br>";
    } else {
        echo "Error creating tbl_admin table: " . $conn->error . "<br>";
        exit();
    }
}

// Check admin credentials
$email = 'admin@gmail.com';
$stmt = $conn->prepare("SELECT admin_id, email, password FROM tbl_admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Admin account not found. Creating it...<br>";
    
    // Create admin account
    $password = '555555';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO tbl_admin (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Email: admin@gmail.com<br>";
        echo "Password: 555555<br>";
    } else {
        echo "Error creating admin account: " . $conn->error . "<br>";
    }
} else {
    $admin = $result->fetch_assoc();
    echo "Admin account exists:<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "ID: " . $admin['admin_id'] . "<br>";
}

$conn->close();
?> 