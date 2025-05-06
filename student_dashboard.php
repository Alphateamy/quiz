<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .welcome-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .feature-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #2196F3;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
        
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($email); ?>!</h2>
            <p>This is your student dashboard. Here you can take quizzes and view your results.</p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="feature-card text-center">
                    <i class="fas fa-tasks feature-icon"></i>
                    <h4>Available Quizzes</h4>
                    <p>Take quizzes created by your teachers.</p>
                    <a href="available_quizzes.php" class="btn btn-primary">View Quizzes</a>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="feature-card text-center">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h4>My Results</h4>
                    <p>View your quiz results and performance.</p>
                    <a href="my_results.php" class="btn btn-primary">View Results</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 