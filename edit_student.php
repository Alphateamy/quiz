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

// Get student ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$student_id = $_GET['id'];

// Fetch student details
$stmt = $conn->prepare("SELECT user_id, email, password FROM tbl_users WHERE user_id = ? AND role = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_students.php");
    exit();
}

$student = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate input
    if (empty($email)) {
        $error_message = "Email is required.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists (excluding current student)
        $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE email = ? AND user_id != ?");
        $stmt->bind_param("si", $email, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Email already exists.";
        } else {
            // Prepare update query based on whether password is being changed
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE tbl_users SET email = ?, password = ? WHERE user_id = ? AND role = 'student'");
                $stmt->bind_param("ssi", $email, $hashed_password, $student_id);
            } else {
                $stmt = $conn->prepare("UPDATE tbl_users SET email = ? WHERE user_id = ? AND role = 'student'");
                $stmt->bind_param("si", $email, $student_id);
            }

            if ($stmt->execute()) {
                $success_message = "Student updated successfully!";
                // Refresh student data
                $student['email'] = $email;
            } else {
                $error_message = "Error updating student. Please try again.";
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
    <title>Edit Student - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Student</h2>
            <a href="manage_students.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
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
                               value="<?php echo htmlspecialchars($student['email']); ?>" required>
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