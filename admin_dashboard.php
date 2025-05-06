<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

$admin_email = $_SESSION['admin_email'];

// Fetch statistics
$stats = [
    'teachers' => 0,
    'students' => 0,
    'quizzes' => 0,
    'results' => 0
];

// Get total teachers
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_users WHERE role = 'teacher'");
$stmt->execute();
$result = $stmt->get_result();
$stats['teachers'] = $result->fetch_assoc()['count'];

// Get total students
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_users WHERE role = 'student'");
$stmt->execute();
$result = $stmt->get_result();
$stats['students'] = $result->fetch_assoc()['count'];

// Get total quizzes
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_quiz");
$stmt->execute();
$result = $stmt->get_result();
$stats['quizzes'] = $result->fetch_assoc()['count'];

// Get total results
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_result");
$stmt->execute();
$result = $stmt->get_result();
$stats['results'] = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        .action-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .action-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Admin Dashboard</h2>
            <div>
                <span class="me-3">Welcome, <?php echo htmlspecialchars($admin_email); ?></span>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['teachers']; ?></div>
                    <div class="stat-label">Teachers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon text-success">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['students']; ?></div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon text-info">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['quizzes']; ?></div>
                    <div class="stat-label">Quizzes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['results']; ?></div>
                    <div class="stat-label">Results</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="action-card">
                    <h4><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</h4>
                    <p>Add, view, and remove teachers from the system.</p>
                    <a href="manage_teachers.php" class="btn btn-primary">
                        <i class="fas fa-cogs"></i> Manage Teachers
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="action-card">
                    <h4><i class="fas fa-user-graduate"></i> Manage Students</h4>
                    <p>Add, view, and remove students from the system.</p>
                    <a href="manage_students.php" class="btn btn-primary">
                        <i class="fas fa-cogs"></i> Manage Students
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 