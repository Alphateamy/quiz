<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

$error_message = '';
$success_message = '';

// Get teacher ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_teachers.php");
    exit();
}

$teacher_id = $_GET['id'];

// Fetch teacher details
$stmt = $conn->prepare("SELECT user_id, email, password, passcode FROM tbl_users WHERE user_id = ? AND role = 'teacher'");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_teachers.php");
    exit();
}

$teacher = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $passcode = trim($_POST['passcode']);

    // Validate input
    if (empty($email)) {
        $error_message = "Email is required.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists (excluding current teacher)
        $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already exists.";
        } else {
            // Prepare update query based on whether password is being changed
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE tbl_users SET email = ?, password = ?, passcode = ? WHERE user_id = ? AND role = 'teacher'");
                $stmt->bind_param("sssi", $email, $hashed_password, $passcode, $teacher_id);
            } else {
                $stmt = $conn->prepare("UPDATE tbl_users SET email = ?, passcode = ? WHERE user_id = ? AND role = 'teacher'");
                $stmt->bind_param("ssi", $email, $passcode, $teacher_id);
            }

            if ($stmt->execute()) {
                $success_message = "Teacher updated successfully!";
                // Refresh teacher data
                $teacher['email'] = $email;
                $teacher['passcode'] = $passcode;
            } else {
                $error_message = "Error updating teacher. Please try again.";
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
    <title>Edit Teacher - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Teacher</h2>
            <a href="manage_teachers.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Teachers
            </a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="passcode" class="form-label">Passcode</label>
                        <input type="text" class="form-control" id="passcode" name="passcode" 
                               value="<?php echo htmlspecialchars($teacher['passcode']); ?>" 
                               placeholder="Enter new passcode or leave empty to keep current">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Leave empty to keep current password">
                        <small class="text-muted">Password must be at least 6 characters long</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Leave empty to keep current password">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 