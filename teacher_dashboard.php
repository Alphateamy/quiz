<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch teacher's passcode
$stmt = $conn->prepare("SELECT passcode FROM tbl_users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$passcode = $user['passcode'];

// Fetch all quizzes
$stmt = $conn->prepare("SELECT * FROM tbl_quiz ORDER BY tbl_quiz_id DESC");
$stmt->execute();
$result = $stmt->get_result();
$quizzes = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all student results
$stmt = $conn->prepare("SELECT * FROM tbl_result ORDER BY date_taken DESC");
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .welcome-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .quiz-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .passcode-display {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            text-align: center;
        }
        .passcode-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2196F3;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <a href="logout.php" class="btn btn-danger logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        
        <div class="welcome-card">
            <h2>Teacher Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($email); ?>! Here you can manage quizzes and view student results.</p>
            
            <div class="passcode-display">
                <p class="mb-1">Your Teacher Passcode:</p>
                <div class="passcode-value"><?php echo htmlspecialchars($passcode); ?></div>
                <small class="text-muted">Keep this passcode safe. You will need it to log in.</small>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="quiz-card">
                    <h3>Manage Quizzes</h3>
                    <p>Create, edit, or delete quizzes for your students.</p>
                    <a href="manage_quizzes.php" class="btn btn-primary">
                        <i class="fas fa-tasks"></i> Manage Quizzes
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="quiz-card">
                    <h3>View Results</h3>
                    <p>View and analyze student quiz results.</p>
                    <a href="view_results.php" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> View Results
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="quiz-card">
                    <h3>View Teachers</h3>
                    <p>View all registered teachers and their passcodes.</p>
                    <a href="view_teachers.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> View Teachers
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="quiz-card">
                    <h3>Recent Quiz Results</h3>
                    <?php if (count($results) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Year & Section</th>
                                        <th>Score</th>
                                        <th>Date Taken</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($results, 0, 5) as $result): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($result['quiz_taker']); ?></td>
                                            <td><?php echo htmlspecialchars($result['year_section']); ?></td>
                                            <td><?php echo $result['total_score']; ?></td>
                                            <td><?php echo date('F j, Y, g:i a', strtotime($result['date_taken'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="view_results.php" class="btn btn-link">View All Results</a>
                    <?php else: ?>
                        <p>No quiz results available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 