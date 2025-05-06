<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate input
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Get the first unused passcode
                $stmt = $conn->prepare("SELECT passcode FROM tbl_passcodes WHERE is_used = 0 ORDER BY passcode ASC LIMIT 1 FOR UPDATE");
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $passcode = $row['passcode'];
                    
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new teacher with passcode
                    $stmt = $conn->prepare("INSERT INTO tbl_users (email, password, role, passcode) VALUES (?, ?, 'teacher', ?)");
                    $stmt->bind_param("sss", $email, $hashed_password, $passcode);
                    $stmt->execute();
                    
                    // Mark passcode as used
                    $stmt = $conn->prepare("UPDATE tbl_passcodes SET is_used = 1 WHERE passcode = ?");
                    $stmt->bind_param("s", $passcode);
                    $stmt->execute();
                    
                    // Commit transaction
                    $conn->commit();
                    
                    $success = "Teacher added successfully! Passcode: " . $passcode;
                } else {
                    throw new Exception("No available passcodes");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $error = "Error adding teacher: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .passcode-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Add New Teacher</h2>
            
            <div class="passcode-info">
                <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                <p>When you add a teacher, they will be automatically assigned a unique passcode. This passcode will be displayed after successful registration.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <br>
                    <strong>Please save this passcode as it will be needed for teacher login.</strong>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Password must be at least 6 characters long</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Teacher
                    </button>
                    <a href="manage_teachers.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Teachers
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 