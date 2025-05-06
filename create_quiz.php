<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit();
}


$message = '';

// Handle quiz creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];
    
    $stmt = $conn->prepare("INSERT INTO tbl_quiz (quiz_question, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $question, $option_a, $option_b, $option_c, $option_d, $correct_answer);
    
    if ($stmt->execute()) {
        $message = "Quiz created successfully!";
        // Clear form data after successful submission
        $_POST = array();
    } else {
        $message = "Error creating quiz.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .quiz-form-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .form-label {
            font-weight: 600;
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
        
        <div class="quiz-form-card">
            <h2 class="mb-4">Create New Quiz</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="question" class="form-label">Question</label>
                    <textarea class="form-control" id="question" name="question" rows="3" required><?php echo isset($_POST['question']) ? htmlspecialchars($_POST['question']) : ''; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="option_a" class="form-label">Option A</label>
                    <input type="text" class="form-control" id="option_a" name="option_a" required value="<?php echo isset($_POST['option_a']) ? htmlspecialchars($_POST['option_a']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="option_b" class="form-label">Option B</label>
                    <input type="text" class="form-control" id="option_b" name="option_b" required value="<?php echo isset($_POST['option_b']) ? htmlspecialchars($_POST['option_b']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="option_c" class="form-label">Option C</label>
                    <input type="text" class="form-control" id="option_c" name="option_c" required value="<?php echo isset($_POST['option_c']) ? htmlspecialchars($_POST['option_c']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="option_d" class="form-label">Option D</label>
                    <input type="text" class="form-control" id="option_d" name="option_d" required value="<?php echo isset($_POST['option_d']) ? htmlspecialchars($_POST['option_d']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="correct_answer" class="form-label">Correct Answer</label>
                    <select class="form-select" id="correct_answer" name="correct_answer" required>
                        <option value="">Select correct answer</option>
                        <option value="A" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'A') ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'B') ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'C') ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?php echo (isset($_POST['correct_answer']) && $_POST['correct_answer'] == 'D') ? 'selected' : ''; ?>>D</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Quiz
                </button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 