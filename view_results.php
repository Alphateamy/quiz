<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}



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
    <title>View Results - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .table {
            margin-bottom: 0;
        }
        .back-btn {
            margin-bottom: 20px;
        }
        .score-high {
            color: #28a745;
            font-weight: bold;
        }
        .score-medium {
            color: #ffc107;
            font-weight: bold;
        }
        .score-low {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="teacher_dashboard.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="card">
            <h2 class="mb-4">Quiz Results</h2>
            
            <?php if (count($results) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Year & Section</th>
                                <th>Score</th>
                                <th>Date Taken</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['quiz_taker']); ?></td>
                                    <td><?php echo htmlspecialchars($result['year_section']); ?></td>
                                    <td class="<?php 
                                        if ($result['total_score'] >= 8) {
                                            echo 'score-high';
                                        } elseif ($result['total_score'] >= 5) {
                                            echo 'score-medium';
                                        } else {
                                            echo 'score-low';
                                        }
                                    ?>"><?php echo $result['total_score']; ?></td>
                                    <td><?php echo date('F j, Y, g:i a', strtotime($result['date_taken'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No quiz results available yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 