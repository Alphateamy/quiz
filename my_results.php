<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch student's quiz results
$stmt = $conn->prepare("SELECT * FROM tbl_result WHERE quiz_taker = ? ORDER BY date_taken DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total score and average
$total_score = 0;
$total_quizzes = count($results);
$average_score = 0;

if ($total_quizzes > 0) {
    foreach ($results as $result) {
        $total_score += $result['total_score'];
    }
    $average_score = $total_score / $total_quizzes;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results - Online Quiz System</title>
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
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .result-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .result-card:hover {
            transform: translateY(-5px);
        }
        .score-badge {
            font-size: 1.2rem;
            padding: 10px 15px;
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
            <h2>My Quiz Results</h2>
            <p>Welcome, <?php echo htmlspecialchars($email); ?>! Here are your quiz results.</p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <h3>Total Quizzes</h3>
                    <h2 class="text-primary"><?php echo $total_quizzes; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3>Total Score</h3>
                    <h2 class="text-success"><?php echo $total_score; ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h3>Average Score</h3>
                    <h2 class="text-info"><?php echo number_format($average_score, 2); ?></h2>
                </div>
            </div>
        </div>
        
        <?php if (count($results) > 0): ?>
            <h3 class="mb-4">Quiz History</h3>
            <div class="row">
                <?php foreach ($results as $result): ?>
                    <div class="col-md-6">
                        <div class="result-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Quiz Result #<?php echo $result['tbl_result_id']; ?></h4>
                                <span class="badge bg-<?php echo $result['total_score'] > 0 ? 'success' : 'danger'; ?> score-badge">
                                    Score: <?php echo $result['total_score']; ?>
                                </span>
                            </div>
                            <p><strong>Date Taken:</strong> <?php echo date('F j, Y, g:i a', strtotime($result['date_taken'])); ?></p>
                            <p><strong>Year & Section:</strong> <?php echo htmlspecialchars($result['year_section']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>You haven't taken any quizzes yet. <a href="available_quizzes.php">Take a quiz now</a>!</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 