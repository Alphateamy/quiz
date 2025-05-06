<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'teacher') {
        header("Location: teacher_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .landing-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .header p {
            color: #666;
            font-size: 1.2rem;
        }
        .role-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .role-card:hover {
            transform: translateY(-5px);
        }
        .role-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #2196F3;
        }
        .role-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        .role-description {
            color: #666;
            margin-bottom: 25px;
        }
        .btn-container {
            display: flex;
            gap: 10px;
        }
        .btn-container .btn {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container landing-container">
        <div class="header">
            <h1>Welcome to Online Quiz System</h1>
            <p>Choose your role to get started</p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="role-card text-center">
                    <i class="fas fa-user-shield role-icon"></i>
                    <h3 class="role-title">Admin</h3>
                    <p class="role-description">Manage the entire system, including teachers, students, quizzes, and results.</p>
                    <div class="btn-container">
                        <a href="admin_login.php" class="btn btn-danger">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="role-card text-center">
                    <i class="fas fa-chalkboard-teacher role-icon"></i>
                    <h3 class="role-title">Teacher</h3>
                    <p class="role-description">Create and manage quizzes, view student results, and track performance.</p>
                    <div class="btn-container">
                        <a href="teacher_login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="teacher_signup.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="role-card text-center">
                    <i class="fas fa-user-graduate role-icon"></i>
                    <h3 class="role-title">Student</h3>
                    <p class="role-description">Take quizzes, view your results, and track your progress.</p>
                    <div class="btn-container">
                        <a href="student_login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="student_signup.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>