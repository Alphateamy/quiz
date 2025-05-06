<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$message = '';
$quiz = null;

// Check if quiz ID is provided
if (isset($_GET['id'])) {
    $quiz_id = $_GET['id'];
    
    // Fetch quiz details
    $stmt = $conn->prepare("SELECT * FROM tbl_quiz WHERE tbl_quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $quiz = $result->fetch_assoc();
    } else {
        $message = "Quiz not found.";
    }
} else {
    $message = "No quiz ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];
    
    // Update quiz
    $stmt = $conn->prepare("UPDATE tbl_quiz SET quiz_question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE tbl_quiz_id = ?");
    $stmt->bind_param("ssssssi", $question, $option_a, $option_b, $option_c, $option_d, $correct_answer, $quiz_id);
    
    if ($stmt->execute()) {
        $message = "Quiz updated successfully!";
        
        // Refresh quiz data
        $stmt = $conn->prepare("SELECT * FROM tbl_quiz WHERE tbl_quiz_id = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
    } else {
        $message = "Error updating quiz.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
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
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="manage_quizzes.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Back to Manage Quizzes
        </a>
        
        <div class="card">
            <h2 class="mb-4">Edit Quiz</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($quiz): ?>
                <form method="POST" action="">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['tbl_quiz_id']; ?>">
                    
                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <textarea class="form-control" id="question" name="question" rows="3" required><?php echo htmlspecialchars($quiz['quiz_question']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="option_a" class="form-label">Option A</label>
                        <input type="text" class="form-control" id="option_a" name="option_a" value="<?php echo htmlspecialchars($quiz['option_a']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="option_b" class="form-label">Option B</label>
                        <input type="text" class="form-control" id="option_b" name="option_b" value="<?php echo htmlspecialchars($quiz['option_b']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="option_c" class="form-label">Option C</label>
                        <input type="text" class="form-control" id="option_c" name="option_c" value="<?php echo htmlspecialchars($quiz['option_c']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="option_d" class="form-label">Option D</label>
                        <input type="text" class="form-control" id="option_d" name="option_d" value="<?php echo htmlspecialchars($quiz['option_d']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Correct Answer</label>
                        <select class="form-select" id="correct_answer" name="correct_answer" required>
                            <option value="A" <?php echo ($quiz['correct_answer'] == 'A') ? 'selected' : ''; ?>>A</option>
                            <option value="B" <?php echo ($quiz['correct_answer'] == 'B') ? 'selected' : ''; ?>>B</option>
                            <option value="C" <?php echo ($quiz['correct_answer'] == 'C') ? 'selected' : ''; ?>>C</option>
                            <option value="D" <?php echo ($quiz['correct_answer'] == 'D') ? 'selected' : ''; ?>>D</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="update_quiz" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Quiz
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">Quiz not found or no quiz ID provided.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 