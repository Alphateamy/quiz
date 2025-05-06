<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$message = '';

// Handle quiz deletion
if (isset($_POST['delete_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $stmt = $conn->prepare("DELETE FROM tbl_quiz WHERE tbl_quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    if ($stmt->execute()) {
        $message = "Quiz deleted successfully!";
    } else {
        $message = "Error deleting quiz.";
    }
}

// Fetch all quizzes
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
    <title>Manage Quizzes - Online Quiz System</title>
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
        <a href="teacher_dashboard.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
        
        <div class="welcome-card">
            <h2>Manage Quizzes</h2>
            <p>Welcome, <?php echo htmlspecialchars($email); ?>! Here you can manage your quizzes.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="quiz-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>All Quizzes</h3>
                <a href="create_quiz.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Quiz
                </a>
            </div>
            
            <?php if (count($quizzes) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Correct Answer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td><?php echo $quiz['tbl_quiz_id']; ?></td>
                                    <td><?php echo htmlspecialchars($quiz['quiz_question']); ?></td>
                                    <td>
                                        A: <?php echo htmlspecialchars($quiz['option_a']); ?><br>
                                        B: <?php echo htmlspecialchars($quiz['option_b']); ?><br>
                                        C: <?php echo htmlspecialchars($quiz['option_c']); ?><br>
                                        D: <?php echo htmlspecialchars($quiz['option_d']); ?>
                                    </td>
                                    <td><?php echo $quiz['correct_answer']; ?></td>
                                    <td>
                                        <a href="edit_quiz.php?id=<?php echo $quiz['tbl_quiz_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['tbl_quiz_id']; ?>">
                                            <button type="submit" name="delete_quiz" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No quizzes available. <a href="create_quiz.php">Create your first quiz</a>!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 