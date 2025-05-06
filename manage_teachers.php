<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle teacher deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $teacher_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tbl_users WHERE user_id = ? AND role = 'teacher'");
    $stmt->bind_param("i", $teacher_id);
    if ($stmt->execute()) {
        $success = "Teacher deleted successfully!";
    } else {
        $error = "Error deleting teacher: " . $conn->error;
    }
}

// Fetch all teachers
$stmt = $conn->prepare("SELECT user_id, email, role, passcode, created_at FROM tbl_users WHERE role = 'teacher' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$teachers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .teacher-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .teacher-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .teacher-email {
            font-weight: bold;
            color: #333;
        }
        .teacher-date {
            color: #666;
            font-size: 0.9em;
        }
        .action-buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Teachers</h2>
            <a href="add_teacher.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New Teacher
            </a>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($teachers)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No teachers found.</div>
                </div>
            <?php else: ?>
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
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo $teacher['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['role']); ?></td>
                                <td><?php echo $teacher['passcode'] ? htmlspecialchars($teacher['passcode']) : 'NULL'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($teacher['created_at'])); ?></td>
                                <td>
                                    <a href="edit_teacher.php?id=<?php echo $teacher['user_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $teacher['user_id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this teacher?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 