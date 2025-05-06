<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle student deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $student_id = $_GET['delete'];

    // Fetch the student's email using user_id
    $stmt = $conn->prepare("SELECT email FROM tbl_users WHERE user_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_email);
    $stmt->fetch();
    $stmt->close();

    if ($student_email) {
        // Delete all results for this student
        $stmt = $conn->prepare("DELETE FROM tbl_result WHERE quiz_taker = ?");
        $stmt->bind_param("s", $student_email);
        $stmt->execute();
        $stmt->close();

        // Delete the student from tbl_users
        $stmt = $conn->prepare("DELETE FROM tbl_users WHERE user_id = ? AND role = 'student'");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success_message = "Student deleted successfully!";
        } else {
            $error_message = "Failed to delete student.";
        }
        $stmt->close();
    } else {
        $error_message = "Student not found.";
    }
}

// Fetch all students
$stmt = $conn->prepare("SELECT * FROM tbl_users WHERE role = 'student' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Students</h2>
            <div>
                <a href="admin_dashboard.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="add_student.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Passcode</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['role']); ?></td>
                                    <td><?php echo $student['passcode'] ? htmlspecialchars($student['passcode']) : 'NULL'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_student.php?id=<?php echo $student['user_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $student['user_id']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this student? This will also delete all their quiz results.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 