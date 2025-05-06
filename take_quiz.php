<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$error = '';
$success = '';

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: available_quizzes.php");
    exit();
}

$quiz_id = (int)$_GET['id'];

// Fetch quiz details
$stmt = $conn->prepare("SELECT * FROM tbl_quiz WHERE tbl_quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: available_quizzes.php");
    exit();
}

$quiz = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    
    if (empty($selected_answer)) {
        $error = "Please select an answer";
    } else {
        // Check if answer is correct
        $is_correct = ($selected_answer === $quiz['correct_answer']);
        
        // Get year and section from session or default values
        $year_section = isset($_SESSION['year_section']) ? $_SESSION['year_section'] : 'Not Specified';
        
        // Calculate score (1 point for correct answer)
        $score = $is_correct ? 1 : 0;
        
        // Insert result into database
        $stmt = $conn->prepare("INSERT INTO tbl_result (quiz_taker, year_section, total_score) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $year_section, $score);
        
        if ($stmt->execute()) {
            $success = $is_correct ? 
                "Congratulations! Your answer is correct. You earned 1 point." : 
                "Sorry, your answer is incorrect. The correct answer is: " . $quiz['correct_answer'];
            
            // Redirect to results page after 3 seconds
            header("refresh:3;url=my_results.php");
        } else {
            $error = "Failed to save your result. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - Online Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .quiz-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .quiz-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .quiz-question {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 25px;
        }
        .option-label {
            display: block;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .option-label:hover {
            background-color: #f0f0f0;
        }
        .option-input {
            margin-right: 10px;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container quiz-container">
        <a href="available_quizzes.php" class="btn btn-secondary back-btn">
            <i class="fas fa-arrow-left"></i> Back to Quizzes
        </a>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="quiz-card">
            <h2 class="quiz-question"><?php echo htmlspecialchars($quiz['quiz_question']); ?></h2>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="option-label">
                        <input type="radio" name="answer" value="A" class="option-input" required>
                        A: <?php echo htmlspecialchars($quiz['option_a']); ?>
                    </label>
                </div>
                
                <div class="mb-3">
                    <label class="option-label">
                        <input type="radio" name="answer" value="B" class="option-input">
                        B: <?php echo htmlspecialchars($quiz['option_b']); ?>
                    </label>
                </div>
                
                <div class="mb-3">
                    <label class="option-label">
                        <input type="radio" name="answer" value="C" class="option-input">
                        C: <?php echo htmlspecialchars($quiz['option_c']); ?>
                    </label>
                </div>
                
                <div class="mb-3">
                    <label class="option-label">
                        <input type="radio" name="answer" value="D" class="option-input">
                        D: <?php echo htmlspecialchars($quiz['option_d']); ?>
                    </label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Submit Answer</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 