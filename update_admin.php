<?php
require_once 'config.php';

// Admin credentials
$email = 'admin@gmail.com';
$password = '555555';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update admin credentials
$stmt = $conn->prepare("UPDATE tbl_admin SET email = ?, password = ? WHERE admin_id = 1");
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin credentials updated successfully!";
} else {
    echo "Error updating admin credentials: " . $conn->error;
}

$stmt->close();
$conn->close();
?> 