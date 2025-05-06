<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}



// Fetch available quizzes from the database
$stmt = $conn->prepare("SELECT * FROM tbl_quiz ORDER BY tbl_quiz_id DESC");
$stmt->execute();
$result = $stmt->get_result();
$quizzes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Quizzes - Online Quiz System</title>
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
        .quiz-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .quiz-card:hover {
            transform: translateY(-5px);
        }
        .quiz-title {
            color: #333;
            margin-bottom: 15px;
        }
        .quiz-info {
            color: #666;
            margin-bottom: 20px;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <a href="student_dashboard.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
        
        <div class="welcome-card">
            <h2>Available Quizzes</h2>
            <p>Here are the quizzes available for you to take.</p>
        </div>
        
        <?php if (count($quizzes) > 0): ?>
            <div class="row">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="col-md-6">
                        <div class="quiz-card">
                            <h3 class="quiz-title">Quiz #<?php echo $quiz['tbl_quiz_id']; ?></h3>
                            <div class="quiz-info">
                                <p><strong>Question:</strong> <?php echo htmlspecialchars($quiz['quiz_question']); ?></p>
                                <p><strong>Options:</strong></p>
                                <ul>
                                    <li>A: <?php echo htmlspecialchars($quiz['option_a']); ?></li>
                                    <li>B: <?php echo htmlspecialchars($quiz['option_b']); ?></li>
                                    <li>C: <?php echo htmlspecialchars($quiz['option_c']); ?></li>
                                    <li>D: <?php echo htmlspecialchars($quiz['option_d']); ?></li>
                                </ul>
                            </div>
                            <a href="take_quiz.php?id=<?php echo $quiz['tbl_quiz_id']; ?>" class="btn btn-primary">Take Quiz</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No quizzes are available at the moment. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 