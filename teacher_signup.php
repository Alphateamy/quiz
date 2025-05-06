<?php
session_start();
require_once 'config.php';

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
            // Get the next available passcode
            $stmt = $conn->prepare("SELECT next_passcode FROM tbl_passcode_counter WHERE id = 1 FOR UPDATE");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $passcode = $row['next_passcode'];
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Insert new teacher with passcode
                    $stmt = $conn->prepare("INSERT INTO tbl_users (email, password, role, passcode) VALUES (?, ?, 'teacher', ?)");
                    $stmt->bind_param("sss", $email, $hashed_password, $passcode);
                    $stmt->execute();
                    
                    // Increment the passcode counter
                    $next_passcode = $passcode + 1;
                    $stmt = $conn->prepare("UPDATE tbl_passcode_counter SET next_passcode = ? WHERE id = 1");
                    $stmt->bind_param("i", $next_passcode);
                    $stmt->execute();
                    
                    // Commit transaction
                    $conn->commit();
                    
                    $success = "Registration successful! Redirecting to login page...";
                    // Redirect to teacher login page after 2 seconds
                    header("refresh:2;url=teacher_login.php");
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $error = "Registration failed. Please try again.";
                }
            } else {
                $error = "System error. Please contact administrator.";
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
    <title>Teacher Registration - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .signup-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
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
        <div class="signup-container">
            <h2 class="form-title">Teacher Registration</h2>
            
            <div class="passcode-info">
                <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                <p>When you register, you will be automatically assigned a unique teacher passcode. Please save this passcode as you will need it to log in.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
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
                        <i class="fas fa-user-plus"></i> Register as Teacher
                    </button>
                </div>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="teacher_login.php">Login here</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 